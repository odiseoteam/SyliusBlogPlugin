<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class BulletListNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'bulletList';
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return implode("\n", array_map(
            static fn (string $item): string => MarkdownLines::prefix($item, '- ', '  '),
            $children,
        ));
    }
}
