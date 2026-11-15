<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class ParagraphNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'paragraph';
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return MarkdownLines::escapeLineStarts(implode('', $children));
    }
}
