<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Odiseo\SyliusBlogPlugin\Content\Extractor\ReadingTime;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ContentRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly ReadingTime $readingTime)
    {
    }

    /** Minutes to read the article in the current locale. */
    public function readingTime(ArticleInterface $article): int
    {
        return $this->readingTime->minutes($article->getTranslation()->getWordCount());
    }
}
