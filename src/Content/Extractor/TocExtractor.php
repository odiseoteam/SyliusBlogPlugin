<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Registry\ContentRegistry;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Webmozart\Assert\Assert;

/** The headings of a document in order. The anchors come from the text, with a number when it repeats. */
final class TocExtractor
{
    public function __construct(
        private readonly ContentRegistry $registry,
        private readonly PlainTextExtractor $plainText,
    ) {
    }

    /** @return list<TocEntry> */
    public function extract(Node $document): array
    {
        $entries = [];
        $used = [];
        $this->collect($document, $entries, $used);

        return $entries;
    }

    /**
     * @param list<TocEntry> $entries
     * @param array<string, true> $used
     */
    private function collect(Node $node, array &$entries, array &$used): void
    {
        // What the blog does not know is not drawn, so its headings are not listed.
        if ('doc' !== $node->type && !$node->isText() && null === $this->registry->getNode($node->type)) {
            return;
        }

        $heading = $this->registry->getNode('heading');

        if (null !== $heading && 'heading' === $node->type) {
            $text = $this->plainText->extract($node);
            if ('' !== $text) {
                $level = $heading->getVariables($node)['level'] ?? null;
                Assert::integer($level, 'The heading node has to give its level.');

                $entries[] = new TocEntry($node, $level, $text, $this->anchor($text, $used));
            }
        }

        foreach ($node->children as $child) {
            $this->collect($child, $entries, $used);
        }
    }

    /** @param array<string, true> $used */
    private function anchor(string $text, array &$used): string
    {
        $base = (new AsciiSlugger())->slug($text)->lower()->toString();
        $base = '' === $base ? 'section' : $base;

        $anchor = $base;
        for ($number = 2; isset($used[$anchor]); ++$number) {
            $anchor = $base . '-' . $number;
        }

        $used[$anchor] = true;

        return $anchor;
    }
}
