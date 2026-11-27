<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Content;

use Doctrine\ORM\PersistentCollection;
use Odiseo\SyliusBlogPlugin\Content\Product\ChannelProductFinder;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ChannelProductFinderTest extends DatabaseTestCase
{
    private Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->channel = $this->channel('FINDER_WEB');
        // The second channel reuses the locale and currency of the first one.
        $this->entityManager->flush();
        $other = $this->channel('FINDER_MOBILE');
        $this->product('FINDER_MUG', [$this->channel]);
        $this->product('FINDER_POT', [$this->channel, $other]);
        $this->product('FINDER_CUP', [$other]);
        $this->product('FINDER_OLD', [$this->channel])->setEnabled(false);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function testItFindsTheEnabledProductsOfTheChannel(): void
    {
        $finder = $this->finder($this->channel->getCode());

        self::assertSame('FINDER_MUG', $finder->find('FINDER_MUG')?->getCode());
        self::assertSame('FINDER_POT', $finder->find('FINDER_POT')?->getCode());
        self::assertNull($finder->find('FINDER_CUP'));
        self::assertNull($finder->find('FINDER_OLD'));
        self::assertNull($finder->find('FINDER_GONE'));
    }

    public function testPreloadingLoadsWhatTheCardShows(): void
    {
        $finder = $this->finder($this->channel->getCode());
        $finder->preload(['FINDER_MUG', 'FINDER_POT', 'FINDER_GONE']);

        $product = $finder->find('FINDER_POT');
        self::assertInstanceOf(ProductInterface::class, $product);
        $variant = $product->getVariants()->first();
        self::assertInstanceOf(ProductVariantInterface::class, $variant);
        foreach ([$product->getImages(), $product->getVariants(), $variant->getChannelPricings()] as $collection) {
            self::assertTrue(!$collection instanceof PersistentCollection || $collection->isInitialized());
        }
        self::assertSame(1000, $variant->getChannelPricingForChannel($this->channel)?->getPrice());
    }

    public function testWithoutChannelThereAreNoProducts(): void
    {
        self::assertNull($this->finder(null)->find('FINDER_MUG'));
    }

    private function finder(?string $channelCode): ChannelProductFinder
    {
        $channel = null === $channelCode ? null : $this->entityManager->getRepository(Channel::class)->findOneBy(['code' => $channelCode]);
        $context = new class($channel) implements ChannelContextInterface {
            public function __construct(private readonly ?ChannelInterface $channel)
            {
            }

            public function getChannel(): ChannelInterface
            {
                return $this->channel ?? throw new ChannelNotFoundException();
            }
        };

        return new ChannelProductFinder($this->entityManager, $context, Product::class);
    }

    /** @param list<Channel> $channels */
    private function product(string $code, array $channels): Product
    {
        $product = new Product();
        $product->setCurrentLocale('en_US');
        $product->setFallbackLocale('en_US');
        $product->setCode($code);
        $product->setName(ucfirst(strtolower($code)));
        $product->setSlug(strtolower($code));

        $variant = new ProductVariant();
        $variant->setCode($code . '_VARIANT');
        foreach ($channels as $channel) {
            $product->addChannel($channel);
            $pricing = new ChannelPricing();
            $pricing->setChannelCode((string) $channel->getCode());
            $pricing->setPrice(1000);
            $variant->addChannelPricing($pricing);
        }
        $product->addVariant($variant);
        $this->entityManager->persist($product);

        return $product;
    }
}
