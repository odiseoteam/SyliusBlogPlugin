<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Odiseo\SyliusBlogPlugin\Content\Document\DocumentParser;
use Odiseo\SyliusBlogPlugin\Content\Extractor\ReadingTime;
use Odiseo\SyliusBlogPlugin\Content\Renderer\HtmlRenderer;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

final class ContentRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ReadingTime $readingTime,
        private readonly DocumentParser $parser,
        private readonly HtmlRenderer $htmlRenderer,
    ) {
    }

    /** The HTML of the content of the article in the current locale, drawn on each call. */
    public function content(ArticleInterface $article): Markup
    {
        return new Markup($this->htmlRenderer->render($this->parser->parse($article->getContent())), 'UTF-8');
    }

    /** Minutes to read the article in the current locale. */
    public function readingTime(ArticleInterface $article): int
    {
        return $this->readingTime->minutes($article->getTranslation()->getWordCount());
    }
}
