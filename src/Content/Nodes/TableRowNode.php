<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class TableRowNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'tableRow';
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return '| ' . implode(' | ', $children) . ' |';
    }
}
