<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Product;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Finds the enabled products of the current channel, with what their card shows (translations, images,
 * variants and prices) loaded in one go. Each product is looked up once per request.
 */
final class ChannelProductFinder implements ProductFinderInterface, ResetInterface
{
    /** @var array<string, ProductInterface|null> */
    private array $found = [];

    /** @param class-string<ProductInterface> $productClass */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly ChannelContextInterface $channelContext,
        private readonly string $productClass,
    ) {
    }

    public function find(string $code): ?ProductInterface
    {
        $this->preload([$code]);

        return $this->found[$this->key($code)] ?? null;
    }

    public function preload(array $codes): void
    {
        $channel = $this->channel();
        if (null === $channel) {
            return;
        }

        $missing = array_values(array_filter($codes, fn (string $code): bool => !array_key_exists($this->key($code), $this->found)));
        if ([] === $missing) {
            return;
        }

        /** @var list<ProductInterface> $products */
        $products = $this->manager->createQueryBuilder()
            ->select('product', 'translation')
            ->from($this->productClass, 'product')
            ->leftJoin('product.translations', 'translation')
            ->andWhere('product.code IN (:codes)')
            ->andWhere(':channel MEMBER OF product.channels')
            ->andWhere('product.enabled = true')
            ->setParameter('codes', $missing)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult()
        ;

        // One query per collection: joining them all at once would multiply the rows.
        if ([] !== $products) {
            $this->load($products, 'product.images', 'image');
            $this->load($products, 'product.variants', 'variant', ['variant.channelPricings' => 'channelPricing']);
        }

        foreach ($missing as $code) {
            $this->found[$this->key($code)] = null;
        }
        foreach ($products as $product) {
            $this->found[$this->key((string) $product->getCode())] = $product;
        }
    }

    public function reset(): void
    {
        $this->found = [];
    }

    /**
     * @param list<ProductInterface> $products
     * @param array<string, string> $joins
     */
    private function load(array $products, string $collection, string $alias, array $joins = []): void
    {
        $builder = $this->manager->createQueryBuilder()
            ->select('product', $alias)
            ->from($this->productClass, 'product')
            ->leftJoin($collection, $alias)
            ->andWhere('product IN (:products)')
            ->setParameter('products', $products)
        ;
        foreach ($joins as $join => $joinAlias) {
            $builder->addSelect($joinAlias)->leftJoin($join, $joinAlias);
        }

        $builder->getQuery()->getResult();
    }

    private function channel(): ?ChannelInterface
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException) {
            return null;
        }

        return $channel instanceof ChannelInterface ? $channel : null;
    }

    private function key(string $code): string
    {
        return $this->channel()?->getCode() . '|' . $code;
    }
}
