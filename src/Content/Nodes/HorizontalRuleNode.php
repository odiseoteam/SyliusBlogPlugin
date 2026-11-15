<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

final class HorizontalRuleNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'horizontalRule';
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return '---';
    }
}
