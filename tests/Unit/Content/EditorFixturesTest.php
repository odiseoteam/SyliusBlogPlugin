<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Extractor\WordCounter;
use Odiseo\SyliusBlogPlugin\Content\Validator\ContentValidator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

/** The content the editor saves, written by its JavaScript tests, is accepted and drawn by the server. */
#[CoversNothing]
final class EditorFixturesTest extends TestCase
{
    /** @return iterable<string, array{string, int}> */
    public static function fixtures(): iterable
    {
        $files = glob(__DIR__ . '/../../Fixtures/Content/Editor/*.json');
        self::assertNotEmpty($files);

        foreach ($files as $file) {
            /** @var array{content: string, words: int} $fixture */
            $fixture = json_decode((string) file_get_contents($file), true, flags: \JSON_THROW_ON_ERROR);

            yield basename($file, '.json') => [$fixture['content'], $fixture['words']];
        }
    }

    #[DataProvider('fixtures')]
    public function testTheServerAcceptsAndDrawsWhatTheEditorSaves(string $content, int $words): void
    {
        $pipeline = new ContentPipeline();

        self::assertSame([], (new ContentValidator($pipeline->registry, Validation::createValidator(), 1048576, 20))->validate($content));

        $document = $pipeline->parser->parse($content);
        self::assertNotSame('', $pipeline->html->render($document));
        self::assertNotSame('', $pipeline->markdown->render($document));
        self::assertSame($words, (new WordCounter())->count($pipeline->plainText->extract($document)), 'The editor and the server count the same words.');
    }
}
