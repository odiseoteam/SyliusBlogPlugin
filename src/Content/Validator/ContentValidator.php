<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Validator;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** Checks the JSON of a content before it is saved. */
final class ContentValidator
{
    private const MAX_VIOLATIONS = 5;

    public function __construct(
        private readonly ContentRegistry $registry,
        private readonly ValidatorInterface $validator,
        private readonly int $maxSize,
        private readonly int $maxDepth,
    ) {
    }

    /** @return list<ContentViolation> */
    public function validate(string $json): array
    {
        if (strlen($json) > $this->maxSize) {
            return [new ContentViolation('odiseo_blog.content.too_large', ['{{ limit }}' => $this->formatSize($this->maxSize)])];
        }

        try {
            $document = json_decode($json, true, DocumentParser::JSON_DEPTH, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [new ContentViolation('odiseo_blog.content.invalid_json')];
        }

        if (!is_array($document) || 'doc' !== ($document['type'] ?? null)) {
            return [new ContentViolation('odiseo_blog.content.invalid_document')];
        }

        $violations = [];
        $this->checkNode($document, 1, $violations);

        return array_slice($violations, 0, self::MAX_VIOLATIONS);
    }

    /**
     * @param array<mixed> $node
     * @param list<ContentViolation> $violations
     */
    private function checkNode(array $node, int $depth, array &$violations): void
    {
        if (count($violations) >= self::MAX_VIOLATIONS) {
            return;
        }

        if ($depth > $this->maxDepth) {
            $violations[] = new ContentViolation('odiseo_blog.content.too_deep', ['{{ limit }}' => (string) $this->maxDepth]);

            return;
        }

        $type = $node['type'] ?? null;
        $attributes = $node['attrs'] ?? [];
        $children = $node['content'] ?? [];
        $marks = $node['marks'] ?? [];
        if (!is_string($type) || !is_array($attributes) || !is_array($children) || !is_array($marks)) {
            $violations[] = new ContentViolation('odiseo_blog.content.invalid_node');

            return;
        }

        if ('text' === $type) {
            $this->checkText($node['text'] ?? null, $marks, $violations);
        } elseif ($depth > 1) {
            $definition = $this->registry->getNode($type);
            if (null === $definition) {
                $violations[] = new ContentViolation('odiseo_blog.content.unknown_node', ['{{ type }}' => $type]);

                return;
            }

            $this->checkAttributes($type, $definition->getConstraints(), $attributes, $violations);
        }

        foreach ($children as $child) {
            if (!is_array($child)) {
                $violations[] = new ContentViolation('odiseo_blog.content.invalid_node');

                return;
            }

            $this->checkNode($child, $depth + 1, $violations);
        }
    }

    /**
     * @param array<mixed> $marks
     * @param list<ContentViolation> $violations
     */
    private function checkText(mixed $text, array $marks, array &$violations): void
    {
        if (!is_string($text)) {
            $violations[] = new ContentViolation('odiseo_blog.content.invalid_node');
        }

        foreach ($marks as $mark) {
            $type = is_array($mark) ? ($mark['type'] ?? null) : null;
            $attributes = is_array($mark) ? ($mark['attrs'] ?? []) : null;
            if (!is_string($type) || !is_array($attributes)) {
                $violations[] = new ContentViolation('odiseo_blog.content.invalid_node');

                continue;
            }

            $definition = $this->registry->getMark($type);
            if (null === $definition) {
                $violations[] = new ContentViolation('odiseo_blog.content.unknown_mark', ['{{ type }}' => $type]);

                continue;
            }

            $this->checkAttributes($type, $definition->getConstraints(), $attributes, $violations);
        }
    }

    /**
     * @param array<string, Constraint|list<Constraint>> $constraints
     * @param array<mixed> $attributes
     * @param list<ContentViolation> $violations
     */
    private function checkAttributes(string $type, array $constraints, array $attributes, array &$violations): void
    {
        foreach ($constraints as $name => $rules) {
            if (!array_key_exists($name, $attributes) || null === $attributes[$name]) {
                continue;
            }

            $problems = $this->validator->validate($attributes[$name], $rules);
            if (0 < count($problems)) {
                $violations[] = new ContentViolation('odiseo_blog.content.invalid_attribute', [
                    '{{ type }}' => $type,
                    '{{ attribute }}' => $name,
                    '{{ reason }}' => (string) $problems->get(0)->getMessage(),
                ]);
            }
        }
    }

    private function formatSize(int $bytes): string
    {
        return $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : round($bytes / 1024) . ' KB';
    }
}
