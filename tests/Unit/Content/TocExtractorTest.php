<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Extractor\TocEntry;
use Odiseo\SyliusBlogPlugin\Content\Extractor\TocExtractor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TocExtractor::class)]
final class TocExtractorTest extends TestCase
{
    public function testItListsTheHeadingsWithTheirAnchors(): void
    {
        $pipeline = new ContentPipeline();
        $document = $pipeline->parser->parse((string) file_get_contents(__DIR__ . '/Snapshots/headings.json'));

        $entries = array_map(
            static fn (TocEntry $entry): array => [$entry->level, $entry->text, $entry->id],
            $pipeline->toc->extract($document),
        );

        self::assertSame([
            [2, 'Why coffee?', 'why-coffee'],
            [3, 'Beans & roasts', 'beans-roasts'],
            [2, 'Why coffee?', 'why-coffee-2'],
            [4, 'Ünïcode título', 'unicode-titulo'],
            [2, '¿?', 'section'],
        ], $entries);
    }

    public function testAHeadingInsideANodeTheBlogDoesNotKnowIsNotListed(): void
    {
        $pipeline = new ContentPipeline();
        $document = $pipeline->parser->parse('{"type":"doc","content":[{"type":"mystery","content":[{"type":"heading","content":[{"type":"text","text":"Hidden"}]}]}]}');

        self::assertSame([], $pipeline->toc->extract($document));
    }

    public function testAHeadingWithoutTextIsNotListed(): void
    {
        $pipeline = new ContentPipeline();
        $document = $pipeline->parser->parse('{"type":"doc","content":[{"type":"heading","attrs":{"level":2}}]}');

        self::assertSame([], $pipeline->toc->extract($document));
    }

    public function testAnAnchorDoesNotTakeTheOneOfAnotherHeading(): void
    {
        $pipeline = new ContentPipeline();
        $titles = ['A', 'A', 'A 2', 'A'];
        $document = $pipeline->parser->parse((string) json_encode([
            'type' => 'doc',
            'content' => array_map(static fn (string $title): array => [
                'type' => 'heading',
                'content' => [['type' => 'text', 'text' => $title]],
            ], $titles),
        ]));

        self::assertSame(
            ['a', 'a-2', 'a-2-2', 'a-3'],
            array_map(static fn (TocEntry $entry): string => $entry->id, $pipeline->toc->extract($document)),
        );
    }
}
