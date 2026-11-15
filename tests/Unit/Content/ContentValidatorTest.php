<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Validator\ContentValidator;
use Odiseo\SyliusBlogPlugin\Content\Validator\ContentViolation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

#[CoversClass(ContentValidator::class)]
final class ContentValidatorTest extends TestCase
{
    private const EMPTY_DOCUMENT = '{"type":"doc","content":[]}';

    /** @return iterable<string, array{string}> */
    public static function validContent(): iterable
    {
        yield 'an empty document' => [self::EMPTY_DOCUMENT];
        yield 'a document without content' => ['{"type":"doc"}'];
        yield 'text with marks' => [self::doc('{"type":"paragraph","content":[{"type":"text","text":"Hi","marks":[{"type":"bold"},{"type":"link","attrs":{"href":"https://example.com","rel":"nofollow sponsored","target":"_blank"}}]}]}')];
        yield 'a heading' => [self::doc('{"type":"heading","attrs":{"level":3},"content":[{"type":"text","text":"Hi"}]}')];
        yield 'attributes left empty' => [self::doc('{"type":"heading","attrs":{"level":null}}')];
        yield 'attributes nobody uses' => [self::doc('{"type":"paragraph","attrs":{"textAlign":"left"}}')];
        yield 'a video' => [self::doc('{"type":"video","attrs":{"src":"https://youtu.be/dQw4w9WgXcQ"}}')];
        yield 'a table cell' => [self::doc('{"type":"table","content":[{"type":"tableRow","content":[{"type":"tableCell","attrs":{"colspan":2,"rowspan":1,"colwidth":null}}]}]}')];
        yield 'HTML' => [self::doc('{"type":"html","attrs":{"html":"<p>Hi</p>"}}')];
    }

    #[DataProvider('validContent')]
    public function testItAcceptsValidContent(string $json): void
    {
        self::assertSame([], $this->validate($json));
    }

    /** @return iterable<string, array{string, string, array<string, string>}> */
    public static function invalidContent(): iterable
    {
        yield 'not JSON' => ['<p>Hello</p>', 'odiseo_blog.content.invalid_json', []];
        yield 'a JSON that is not an object' => ['"text"', 'odiseo_blog.content.invalid_document', []];
        yield 'a JSON that is not a document' => ['{"type":"paragraph"}', 'odiseo_blog.content.invalid_document', []];
        yield 'a node without type' => [self::doc('{"content":[]}'), 'odiseo_blog.content.invalid_node', []];
        yield 'a node that is not an object' => [self::doc('"paragraph"'), 'odiseo_blog.content.invalid_node', []];
        yield 'content that is not a list' => [self::doc('{"type":"paragraph","content":"Hi"}'), 'odiseo_blog.content.invalid_node', []];
        yield 'a text without text' => [self::doc('{"type":"paragraph","content":[{"type":"text"}]}'), 'odiseo_blog.content.invalid_node', []];
        yield 'a mark without type' => [self::doc('{"type":"text","text":"Hi","marks":[{"attrs":{}}]}'), 'odiseo_blog.content.invalid_node', []];
        yield 'an unknown node' => [self::doc('{"type":"mystery"}'), 'odiseo_blog.content.unknown_node', ['{{ type }}' => 'mystery']];
        yield 'an unknown node deep inside' => [self::doc('{"type":"blockquote","content":[{"type":"paragraph","content":[{"type":"mystery"}]}]}'), 'odiseo_blog.content.unknown_node', ['{{ type }}' => 'mystery']];
        yield 'a document inside a document' => [self::doc('{"type":"doc","content":[]}'), 'odiseo_blog.content.unknown_node', ['{{ type }}' => 'doc']];
        yield 'an unknown mark' => [self::doc('{"type":"paragraph","content":[{"type":"text","text":"Hi","marks":[{"type":"sparkle"}]}]}'), 'odiseo_blog.content.unknown_mark', ['{{ type }}' => 'sparkle']];
        yield 'a heading level' => [self::doc('{"type":"heading","attrs":{"level":1}}'), 'odiseo_blog.content.invalid_attribute', ['{{ type }}' => 'heading', '{{ attribute }}' => 'level']];
        yield 'a javascript link' => [self::doc('{"type":"text","text":"Hi","marks":[{"type":"link","attrs":{"href":"javascript:alert(1)"}}]}'), 'odiseo_blog.content.invalid_attribute', ['{{ type }}' => 'link', '{{ attribute }}' => 'href']];
        yield 'a link relation' => [self::doc('{"type":"text","text":"Hi","marks":[{"type":"link","attrs":{"href":"/a","rel":"follow"}}]}'), 'odiseo_blog.content.invalid_attribute', ['{{ type }}' => 'link', '{{ attribute }}' => 'rel']];
        yield 'a video of another site' => [self::doc('{"type":"video","attrs":{"src":"https://example.com/a.mp4"}}'), 'odiseo_blog.content.invalid_attribute', ['{{ type }}' => 'video', '{{ attribute }}' => 'src']];
        yield 'a code language' => [self::doc('{"type":"codeBlock","attrs":{"language":"php<script>"}}'), 'odiseo_blog.content.invalid_attribute', ['{{ type }}' => 'codeBlock', '{{ attribute }}' => 'language']];
        yield 'an attribute of the wrong type' => [self::doc('{"type":"orderedList","attrs":{"start":"one"}}'), 'odiseo_blog.content.invalid_attribute', ['{{ type }}' => 'orderedList', '{{ attribute }}' => 'start']];
    }

    /**
     * @param array<string, string> $parameters
     */
    #[DataProvider('invalidContent')]
    public function testItRejectsWhatTheBlogCannotDraw(string $json, string $message, array $parameters): void
    {
        $violations = $this->validate($json);

        self::assertCount(1, $violations);
        self::assertSame($message, $violations[0]->message);
        foreach ($parameters as $name => $value) {
            self::assertSame($value, $violations[0]->parameters[$name]);
        }
    }

    public function testItRejectsContentThatIsTooLarge(): void
    {
        $violations = $this->validate(self::EMPTY_DOCUMENT . str_repeat(' ', 2048), maxSize: 2048);

        self::assertSame('odiseo_blog.content.too_large', $violations[0]->message);
        self::assertSame('2 KB', $violations[0]->parameters['{{ limit }}']);
    }

    public function testItRejectsContentThatIsTooDeep(): void
    {
        $nested = self::doc(str_repeat('{"type":"blockquote","content":[', 3) . '{"type":"paragraph"}' . str_repeat(']}', 3));

        self::assertSame([], $this->validate($nested, maxDepth: 5));

        $violations = $this->validate($nested, maxDepth: 4);
        self::assertSame('odiseo_blog.content.too_deep', $violations[0]->message);
        self::assertSame('4', $violations[0]->parameters['{{ limit }}']);
    }

    public function testTheHtmlNodeIsMeasuredInBytes(): void
    {
        $html = static fn (int $characters): string => self::doc((string) json_encode(['type' => 'html', 'attrs' => ['html' => str_repeat('ñ', $characters)]]));

        self::assertSame([], $this->validate($html(50000)));
        self::assertSame('odiseo_blog.content.invalid_attribute', $this->validate($html(50001))[0]->message);
    }

    public function testItReportsAFewProblemsAtATime(): void
    {
        $json = self::doc(implode(',', array_fill(0, 10, '{"type":"mystery"}')));

        self::assertCount(5, $this->validate($json));
    }

    private static function doc(string $nodes): string
    {
        return '{"type":"doc","content":[' . $nodes . ']}';
    }

    /** @return list<ContentViolation> */
    private function validate(string $json, int $maxSize = 1048576, int $maxDepth = 20): array
    {
        $validator = new ContentValidator((new ContentPipeline())->registry, Validation::createValidator(), $maxSize, $maxDepth);

        return $validator->validate($json);
    }
}
