<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

use Webmozart\Assert\Assert;

final class ReadingTime
{
    public function __construct(private readonly int $wordsPerMinute = 200)
    {
        Assert::greaterThan($wordsPerMinute, 0);
    }

    /** Minutes to read the given words, rounded up: 0 only when there are no words. */
    public function minutes(int $words): int
    {
        return $words > 0 ? (int) ceil($words / $this->wordsPerMinute) : 0;
    }
}
