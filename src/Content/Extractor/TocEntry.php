<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

/** A heading of the document, with the anchor to link to it. */
final readonly class TocEntry
{
    public function __construct(
        public Node $node,
        public int $level,
        public string $text,
        public string $id,
    ) {
    }
}
