<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Twig;

use Odiseo\SyliusBlogPlugin\Content\Extractor\ReadingTime;
use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Twig\ContentRuntime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentRuntime::class)]
final class ContentRuntimeTest extends TestCase
{
    public function testTheReadingTimeUsesTheWordsOfTheCurrentLocale(): void
    {
        $article = new Article();
        $article->setFallbackLocale('en_US');
        $article->setCurrentLocale('es_AR');
        $article->getTranslation('en_US')->setWordCount(450);
        $article->getTranslation('es_AR')->setWordCount(90);

        self::assertSame(1, (new ContentRuntime(new ReadingTime()))->readingTime($article));
    }
}
