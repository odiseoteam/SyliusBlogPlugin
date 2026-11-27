<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Twig;

use Odiseo\SyliusBlogPlugin\Content\Extractor\ReadingTime;
use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Twig\ContentRuntime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tests\Odiseo\SyliusBlogPlugin\Unit\Content\ContentPipeline;

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

        self::assertSame(1, $this->runtime()->readingTime($article));
    }

    public function testTheContentIsDrawnAsHtml(): void
    {
        $article = new Article();
        $article->setFallbackLocale('en_US');
        $article->setCurrentLocale('en_US');
        $article->setContent('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Hi"}]}]}');

        $html = $this->runtime()->content($article);

        self::assertSame('<p>Hi</p>', (string) $html);
        $article->setContent(null);
        self::assertSame('', (string) $this->runtime()->content($article));
    }

    private function runtime(): ContentRuntime
    {
        $pipeline = new ContentPipeline();

        return new ContentRuntime(new ReadingTime(), $pipeline->parser, $pipeline->html);
    }
}
