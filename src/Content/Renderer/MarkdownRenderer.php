<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Renderer;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;

/** Writes a document as Markdown. What the blog does not know is left out. */
final class MarkdownRenderer
{
    public function __construct(private readonly ContentRegistry $registry)
    {
    }

    public function render(Node $document): string
    {
        $this->registry->preload($document);

        return implode("\n\n", $this->children($document));
    }

    /** @return list<string> */
    private function children(Node $node): array
    {
        return array_values(array_filter(
            array_map($this->node(...), $node->children),
            static fn (string $markdown): bool => '' !== $markdown,
        ));
    }

    private function node(Node $node): string
    {
        if ($node->isText()) {
            return $this->text($node);
        }

        return $this->registry->getNode($node->type)?->toMarkdown($node, $this->children($node)) ?? '';
    }

    private function text(Node $node): string
    {
        $text = $node->text ?? '';
        $isCode = [] !== array_filter($node->marks, static fn ($mark): bool => 'code' === $mark->type);
        if (!$isCode) {
            $text = (string) preg_replace('/([\\\\`*_\[\]<>])/', '\\\\$1', $text);
        }

        // The marks wrap the words, not the spaces around them.
        $core = trim($text);
        if ('' === $core) {
            return $text;
        }

        $before = substr($text, 0, strlen($text) - strlen(ltrim($text)));
        $after = substr($text, strlen(rtrim($text)));

        // The first mark is the outermost one.
        foreach (array_reverse($node->marks) as $mark) {
            $core = $this->registry->getMark($mark->type)?->toMarkdown($mark, $core) ?? $core;
        }

        return $before . $core . $after;
    }
}
