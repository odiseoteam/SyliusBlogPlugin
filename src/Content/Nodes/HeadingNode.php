<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraints as Assert;

final class HeadingNode extends AbstractContentNode
{
    public const LEVELS = [2, 3, 4];

    public function getType(): string
    {
        return 'heading';
    }

    public function getAttributes(): array
    {
        return ['level' => 2, 'textAlign' => null];
    }

    public function getConstraints(): array
    {
        return ['level' => new Assert\Choice(choices: self::LEVELS), 'textAlign' => TextAlign::constraint()];
    }

    public function getVariables(Node $node): array
    {
        return ['level' => $this->level($node), 'align_class' => TextAlign::classOf($node)];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return str_repeat('#', $this->level($node)) . ' ' . implode('', $children);
    }

    private function level(Node $node): int
    {
        $level = $this->attribute($node, 'level');

        return is_int($level) && in_array($level, self::LEVELS, true) ? $level : 2;
    }
}
