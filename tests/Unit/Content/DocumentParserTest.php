<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Document\Mark;
use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(DocumentParser::class)]
final class DocumentParserTest extends TestCase
{
    public function testItReadsNodesMarksAndAttributes(): void
    {
        $document = (new DocumentParser())->parse(json_encode([
            'type' => 'doc',
            'content' => [
                ['type' => 'heading', 'attrs' => ['level' => 3], 'content' => [
                    ['type' => 'text', 'text' => 'Hi', 'marks' => [['type' => 'link', 'attrs' => ['href' => '/a']]]],
                ]],
            ],
        ], \JSON_THROW_ON_ERROR));

        $heading = $document->children[0];
        self::assertSame('heading', $heading->type);
        self::assertSame(['level' => 3], $heading->attributes);
        self::assertEquals(new Node('text', [], [], [new Mark('link', ['href' => '/a'])], 'Hi'), $heading->children[0]);
    }

    /** @return iterable<string, array{string|null}> */
    public static function unreadableContent(): iterable
    {
        yield 'nothing' => [null];
        yield 'empty' => [''];
        yield 'not JSON' => ['<p>Hello</p>'];
        yield 'a scalar' => ['"text"'];
        yield 'not a document' => ['{"type":"paragraph"}'];
    }

    #[DataProvider('unreadableContent')]
    public function testUnreadableContentIsAnEmptyDocument(?string $json): void
    {
        self::assertEquals(new Node('doc'), (new DocumentParser())->parse($json));
    }

    public function testItReadsTheDeepestContentTheValidatorAccepts(): void
    {
        $levels = 100;
        $json = '{"type":"doc","content":[' . str_repeat('{"type":"blockquote","content":[', $levels - 3)
            . '{"type":"paragraph","content":[{"type":"text","text":"Deep","marks":[{"type":"link","attrs":{"href":"/a"}}]}]}'
            . str_repeat(']}', $levels - 3) . ']}';

        self::assertSame('Deep', (new ContentPipeline())->plainText->extract((new DocumentParser())->parse($json)));
    }

    public function testItLeavesOutWhatIsMalformed(): void
    {
        $document = (new DocumentParser())->parse('{"type":"doc","content":[1,{"type":""},{"type":"paragraph","attrs":"x","content":"y"},{"type":"hardBreak"}]}');

        self::assertSame(['paragraph', 'hardBreak'], array_map(static fn (Node $node): string => $node->type, $document->children));
        self::assertSame([], $document->children[0]->attributes);
        self::assertSame([], $document->children[0]->children);
    }
}
