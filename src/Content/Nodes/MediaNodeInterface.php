<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

/** A node that shows uploaded files. Their paths keep the files from being pruned. */
interface MediaNodeInterface
{
    /** @return list<string> The storage paths of the files the node shows. */
    public function getMediaPaths(Node $node): array;
}
