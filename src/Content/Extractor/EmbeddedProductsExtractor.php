<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ProductNode;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;

/** The codes of the products a document shows, in order and without repeats. Nodes the blog does not know are skipped. */
final class EmbeddedProductsExtractor
{
    public function __construct(private readonly ContentRegistry $registry)
    {
    }

    /** @return list<string> */
    public function extract(Node $node): array
    {
        return array_values(array_unique($this->codes($node)));
    }

    /** @return list<string> */
    private function codes(Node $node): array
    {
        if ('doc' !== $node->type && null === $this->registry->getNode($node->type)) {
            return [];
        }

        $code = $node->attributes['code'] ?? null;
        if ('product' === $node->type && is_string($code) && 1 === preg_match(ProductNode::CODE, $code)) {
            return [$code];
        }

        return array_merge([], ...array_map($this->codes(...), $node->children));
    }
}
