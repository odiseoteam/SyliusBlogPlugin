<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Content;

use Odiseo\SyliusBlogPlugin\Content\Extractor\ReadingTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReadingTime::class)]
final class ReadingTimeTest extends TestCase
{
    /** @return iterable<string, array{int, int}> */
    public static function words(): iterable
    {
        yield 'nothing' => [0, 0];
        yield 'a few words' => [12, 1];
        yield 'exactly a minute' => [200, 1];
        yield 'a word more' => [201, 2];
        yield 'a long read' => [2350, 12];
    }

    #[DataProvider('words')]
    public function testItRoundsTheMinutesUp(int $words, int $minutes): void
    {
        self::assertSame($minutes, (new ReadingTime())->minutes($words));
    }

    public function testTheSpeedCanBeChanged(): void
    {
        self::assertSame(2, (new ReadingTime(100))->minutes(150));
    }

    public function testTheSpeedIsPositive(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ReadingTime(0);
    }
}
