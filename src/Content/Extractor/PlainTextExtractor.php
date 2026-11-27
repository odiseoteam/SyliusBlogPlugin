<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Nodes\TextNodeInterface;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;

/** The text of a document without formatting, with a blank line between blocks. Nodes the blog does not know add nothing. */
final class PlainTextExtractor
{
    public function __construct(private readonly ContentRegistry $registry)
    {
    }

    public function extract(Node $node): string
    {
        return trim((string) preg_replace("/\n{3,}/", "\n\n", $this->text($node)));
    }

    private function text(Node $node): string
    {
        if ($node->isText()) {
            return $node->text ?? '';
        }

        $definition = $this->registry->getNode($node->type);
        if ('doc' !== $node->type && null === $definition) {
            return '';
        }

        if ($definition instanceof TextNodeInterface) {
            return $definition->toText($node);
        }

        if ([] === $node->children) {
            return true === $definition?->isInline() ? "\n" : '';
        }

        $parts = array_map($this->text(...), $node->children);

        return $this->flowsInline($node->children[0])
            ? implode('', $parts)
            : implode("\n\n", array_filter($parts, static fn (string $part): bool => '' !== trim($part)));
    }

    private function flowsInline(Node $node): bool
    {
        return $node->isText() || true === $this->registry->getNode($node->type)?->isInline();
    }
}
