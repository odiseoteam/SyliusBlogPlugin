<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

/** A node without children whose attributes hold text, like a caption. It counts for the plain text and the words. */
interface TextNodeInterface
{
    public function toText(Node $node): string;
}
