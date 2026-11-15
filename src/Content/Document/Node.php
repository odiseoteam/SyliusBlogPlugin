<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Document;

/** A node of a Tiptap (ProseMirror) document. */
final readonly class Node
{
    /**
     * @param array<string, mixed> $attributes
     * @param list<Node> $children
     * @param list<Mark> $marks
     */
    public function __construct(
        public string $type,
        public array $attributes = [],
        public array $children = [],
        public array $marks = [],
        public ?string $text = null,
    ) {
    }

    public function isText(): bool
    {
        return 'text' === $this->type;
    }
}
