<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraints as Assert;

/** The alignment of a block, as the `textAlign` of Tiptap, and the Bootstrap class that draws it. */
final class TextAlign
{
    public const CLASSES = ['left' => 'text-start', 'center' => 'text-center', 'right' => 'text-end'];

    public static function constraint(): Assert\Choice
    {
        return new Assert\Choice(choices: array_keys(self::CLASSES));
    }

    /** The class for the alignment of the node, or null for the default one. */
    public static function classOf(Node $node): ?string
    {
        $align = $node->attributes['textAlign'] ?? null;

        return is_string($align) ? self::CLASSES[$align] ?? null : null;
    }
}
