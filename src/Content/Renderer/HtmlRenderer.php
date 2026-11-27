<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Renderer;

use Odiseo\SyliusBlogPlugin\Content\Document\Mark;
use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Extractor\TocExtractor;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use Twig\Environment;
use Twig\Markup;

/** Draws a document with the Twig template of each node and mark. What the blog does not know is left out. */
final class HtmlRenderer
{
    public function __construct(
        private readonly ContentRegistry $registry,
        private readonly TocExtractor $toc,
        private readonly Environment $twig,
    ) {
    }

    public function render(Node $document): string
    {
        $this->registry->preload($document);

        /** @var \SplObjectStorage<Node, string> $anchors */
        $anchors = new \SplObjectStorage();
        foreach ($this->toc->extract($document) as $entry) {
            $anchors[$entry->node] = $entry->id;
        }

        return $this->children($document, $anchors);
    }

    /** @param \SplObjectStorage<Node, string> $anchors */
    private function children(Node $node, \SplObjectStorage $anchors): string
    {
        return implode('', array_map(fn (Node $child): string => $this->node($child, $anchors), $node->children));
    }

    /** @param \SplObjectStorage<Node, string> $anchors */
    private function node(Node $node, \SplObjectStorage $anchors): string
    {
        if ($node->isText()) {
            return $this->text($node);
        }

        $definition = $this->registry->getNode($node->type);
        if (null === $definition) {
            return '';
        }

        return trim($this->twig->render($definition->getTemplate(), [
            'node' => $node,
            'attributes' => $this->attributes($definition->getAttributes(), $node->attributes),
            'anchor' => $anchors[$node] ?? null,
            'children' => new Markup($this->children($node, $anchors), 'UTF-8'),
            ...$definition->getVariables($node),
        ]));
    }

    private function text(Node $node): string
    {
        $html = htmlspecialchars($node->text ?? '', \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');

        // The first mark is the outermost one.
        foreach (array_reverse($node->marks) as $mark) {
            $html = $this->mark($mark, $html);
        }

        return $html;
    }

    private function mark(Mark $mark, string $html): string
    {
        $definition = $this->registry->getMark($mark->type);
        if (null === $definition) {
            return $html;
        }

        return trim($this->twig->render($definition->getTemplate(), [
            'mark' => $mark,
            'attributes' => $this->attributes($definition->getAttributes(), $mark->attributes),
            'content' => new Markup($html, 'UTF-8'),
            ...$definition->getVariables($mark),
        ]));
    }

    /**
     * @param array<string, mixed> $defaults
     * @param array<string, mixed> $given
     *
     * @return array<string, mixed>
     */
    private function attributes(array $defaults, array $given): array
    {
        return array_replace($defaults, array_intersect_key($given, $defaults));
    }
}
