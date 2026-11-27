<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

/** A node that loads what all its nodes of a document need at once, before the document is drawn. */
interface PreloadingNodeInterface
{
    public function preload(Node $document): void;
}
