<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Extractor\WordCounter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(WordCounter::class)]
final class WordCounterTest extends TestCase
{
    /** @return iterable<string, array{string, int}> */
    public static function texts(): iterable
    {
        // Shared with the editor, so both count the same.
        /** @var list<array{case: string, text: string, words: int}> $cases */
        $cases = json_decode((string) file_get_contents(__DIR__ . '/../../Fixtures/Content/words.json'), true);
        foreach ($cases as $case) {
            yield $case['case'] => [$case['text'], $case['words']];
        }
    }

    #[DataProvider('texts')]
    public function testItCountsWords(string $text, int $count): void
    {
        self::assertSame($count, (new WordCounter())->count($text));
    }
}
