<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Document;

/** Reads stored content. It never fails: what it cannot read is left out. */
final class DocumentParser
{
    /** JSON nesting it reads, enough for the deepest content the validator accepts. */
    public const JSON_DEPTH = 512;

    public function parse(?string $json): Node
    {
        $data = null === $json || '' === $json ? null : json_decode($json, true, self::JSON_DEPTH);
        $document = is_array($data) ? $this->node($data) : null;

        return 'doc' === $document?->type ? $document : new Node('doc');
    }

    /** @param array<mixed> $data */
    private function node(array $data): ?Node
    {
        $type = $data['type'] ?? null;
        if (!is_string($type) || '' === $type) {
            return null;
        }

        $children = [];
        foreach ($this->list($data['content'] ?? null) as $child) {
            $node = $this->node($child);
            if (null !== $node) {
                $children[] = $node;
            }
        }

        $marks = [];
        foreach ($this->list($data['marks'] ?? null) as $mark) {
            if (is_string($mark['type'] ?? null)) {
                $marks[] = new Mark($mark['type'], $this->attributes($mark));
            }
        }

        $text = $data['text'] ?? null;

        return new Node($type, $this->attributes($data), $children, $marks, is_string($text) ? $text : null);
    }

    /** @return list<array<mixed>> */
    private function list(mixed $value): array
    {
        return is_array($value) ? array_values(array_filter($value, is_array(...))) : [];
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<string, mixed>
     */
    private function attributes(array $data): array
    {
        $attributes = $data['attrs'] ?? null;

        return is_array($attributes) ? array_filter($attributes, is_string(...), \ARRAY_FILTER_USE_KEY) : [];
    }
}
