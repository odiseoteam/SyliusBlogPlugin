<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class HardBreakNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'hardBreak';
    }

    public function isInline(): bool
    {
        return true;
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return "  \n";
    }
}
