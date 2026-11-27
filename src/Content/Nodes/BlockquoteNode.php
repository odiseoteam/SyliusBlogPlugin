<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class BlockquoteNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'blockquote';
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return MarkdownLines::prefix(implode("\n\n", $children), '> ', '> ');
    }
}
