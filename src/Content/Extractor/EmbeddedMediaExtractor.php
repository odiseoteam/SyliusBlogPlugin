<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Nodes\MediaNodeInterface;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;

/** The storage paths of the uploaded files a document shows, in order and without repeats. Nodes the blog does not know are skipped. */
final class EmbeddedMediaExtractor
{
    public function __construct(private readonly ContentRegistry $registry)
    {
    }

    /** @return list<string> */
    public function extract(Node $node): array
    {
        return array_values(array_unique($this->paths($node)));
    }

    /** @return list<string> */
    private function paths(Node $node): array
    {
        $definition = $this->registry->getNode($node->type);
        if ('doc' !== $node->type && null === $definition) {
            return [];
        }

        $paths = $definition instanceof MediaNodeInterface ? $definition->getMediaPaths($node) : [];

        return array_merge($paths, ...array_map($this->paths(...), $node->children));
    }
}
