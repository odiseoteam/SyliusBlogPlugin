<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Product\ProductFinderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** A product of the catalog, drawn with the product card of the shop. Not shown when it is not in the channel. */
final class ProductNode extends AbstractContentNode implements PreloadingNodeInterface
{
    public const CODE = '/^[\w-]+$/';

    public function __construct(
        private readonly ProductFinderInterface $productFinder,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function getType(): string
    {
        return 'product';
    }

    public function getAttributes(): array
    {
        return ['code' => null];
    }

    public function getConstraints(): array
    {
        return ['code' => [new Assert\NotBlank(), new Assert\Type('string'), new Assert\Regex(self::CODE)]];
    }

    /** Loads the products of the document with a few queries, instead of a few for each one. */
    public function preload(Node $document): void
    {
        $codes = $this->codes($document);
        if ([] !== $codes) {
            $this->productFinder->preload(array_values(array_unique($codes)));
        }
    }

    public function getVariables(Node $node): array
    {
        return ['product' => $this->product($node)];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        $product = $this->product($node);
        $slug = $product?->getSlug();
        if (null === $product || null === $slug) {
            return '';
        }

        $name = str_replace(['[', ']'], ['\\[', '\\]'], (string) $product->getName());

        try {
            $url = $this->urlGenerator->generate('sylius_shop_product_show', ['slug' => $slug], UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (RoutingException) {
            // Outside a request of the shop there is no locale for the address.
            return $name;
        }

        return sprintf('[%s](%s)', $name, $url);
    }

    /** @return list<string> */
    private function codes(Node $node): array
    {
        if ($this->getType() === $node->type) {
            $code = $this->attribute($node, 'code');

            return is_string($code) && 1 === preg_match(self::CODE, $code) ? [$code] : [];
        }

        return array_merge([], ...array_map($this->codes(...), $node->children));
    }

    private function product(Node $node): ?ProductInterface
    {
        $code = $this->attribute($node, 'code');

        return is_string($code) && 1 === preg_match(self::CODE, $code) ? $this->productFinder->find($code) : null;
    }
}
