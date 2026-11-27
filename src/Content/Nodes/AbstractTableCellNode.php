<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraints as Assert;

/** What a header cell and a data cell have in common. */
abstract class AbstractTableCellNode extends AbstractContentNode
{
    public function getAttributes(): array
    {
        return ['colspan' => 1, 'rowspan' => 1];
    }

    public function getConstraints(): array
    {
        return [
            'colspan' => [new Assert\Type('int'), new Assert\Range(min: 1, max: 100)],
            'rowspan' => [new Assert\Type('int'), new Assert\Range(min: 1, max: 100)],
        ];
    }

    public function getVariables(Node $node): array
    {
        return [
            'colspan' => $this->span($node, 'colspan'),
            'rowspan' => $this->span($node, 'rowspan'),
        ];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return str_replace('|', '\\|', implode(' ', $children));
    }

    private function span(Node $node, string $name): int
    {
        $value = $this->attribute($node, $name);

        return is_int($value) && $value >= 1 && $value <= 100 ? $value : 1;
    }
}
