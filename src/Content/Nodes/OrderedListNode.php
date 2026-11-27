<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderedListNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'orderedList';
    }

    public function getAttributes(): array
    {
        return ['start' => 1];
    }

    public function getConstraints(): array
    {
        return ['start' => [new Assert\Type('int'), new Assert\Range(min: 1, max: 100000)]];
    }

    public function getVariables(Node $node): array
    {
        return ['start' => $this->start($node)];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        $start = $this->start($node);
        $items = [];

        foreach ($children as $index => $item) {
            $marker = ($start + $index) . '. ';
            $items[] = MarkdownLines::prefix($item, $marker, str_repeat(' ', strlen($marker)));
        }

        return implode("\n", $items);
    }

    private function start(Node $node): int
    {
        $start = $this->attribute($node, 'start');

        return is_int($start) && $start >= 1 ? $start : 1;
    }
}
