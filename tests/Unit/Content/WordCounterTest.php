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
        yield 'nothing' => ['', 0];
        yield 'words and lines' => ["Hello brave\n\nnew world", 4];
        yield 'punctuation' => ['Hello, world! (really?)', 3];
        yield 'accents' => ['Café con leche, ¿qué más?', 5];
        yield 'dashes and apostrophes' => ["Don't over-think it", 3];
        yield 'numbers' => ['In 2026 we sold 1,500 cups', 6];
    }

    #[DataProvider('texts')]
    public function testItCountsWords(string $text, int $count): void
    {
        self::assertSame($count, (new WordCounter())->count($text));
    }
}
