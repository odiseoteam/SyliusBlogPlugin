<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class TableNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'table';
    }

    /** GitHub flavored: the first row is the header. */
    public function toMarkdown(Node $node, array $children): string
    {
        if ([] === $children) {
            return '';
        }

        $columns = max(1, count($node->children[0]->children));
        $separator = '| ' . implode(' | ', array_fill(0, $columns, '---')) . ' |';

        return implode("\n", [$children[0], $separator, ...array_slice($children, 1)]);
    }
}
