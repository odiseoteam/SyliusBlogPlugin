<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleAuthor;
use Odiseo\SyliusBlogPlugin\Entity\ArticleImage;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Entity\Author;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Article::class)]
final class ArticleTest extends TestCase
{
    public function testANewArticleIsADraftWithCommentsEnabled(): void
    {
        $article = new Article();

        self::assertSame(ArticleInterface::STATE_DRAFT, $article->getState());
        self::assertTrue($article->isCommentsEnabled());
        self::assertFalse($article->isFeatured());
    }

    public function testAuthorsFollowTheBylinePosition(): void
    {
        $article = new Article();
        $article->addArticleAuthor($this->byline('second', 1));
        $article->addArticleAuthor($this->byline('first', 0));

        self::assertSame(['first', 'second'], array_map(static fn ($author) => $author->getCode(), $article->getAuthors()));
    }

    public function testABylineIsLinkedToItsArticle(): void
    {
        $article = new Article();
        $byline = $this->byline('jane', 0);

        $article->addArticleAuthor($byline);
        self::assertSame($article, $byline->getArticle());

        $article->removeArticleAuthor($byline);
        self::assertNull($byline->getArticle());
        self::assertCount(0, $article->getArticleAuthors());
    }

    public function testSettingTheAuthorsKeepsTheBylinesThatStay(): void
    {
        $jane = $this->author('jane');
        $john = $this->author('john');
        $ann = $this->author('ann');
        $article = new Article();
        $article->setAuthors([$jane, $john]);
        $janeByline = $article->getArticleAuthors()->first();

        $article->setAuthors([$ann, $jane]);

        self::assertSame([$ann, $jane], $article->getAuthors());
        self::assertCount(2, $article->getArticleAuthors());
        self::assertTrue($article->hasArticleAuthor($janeByline ?: throw new \LogicException()));
        self::assertSame(1, $janeByline->getPosition());
    }

    public function testAnAuthorSignsOnce(): void
    {
        $jane = $this->author('jane');
        $article = new Article();

        $article->setAuthors([$jane, $jane]);

        self::assertCount(1, $article->getArticleAuthors());
        self::assertSame([$jane], $article->getAuthors());
    }

    public function testTranslatedFieldsGoToTheCurrentLocale(): void
    {
        $article = new Article();
        $article->setCurrentLocale('es_AR');
        $article->setFallbackLocale('en_US');
        $article->setTitle('Hola');
        $article->setContent('{"type":"doc","content":[]}');

        self::assertSame('Hola', $article->getTranslation('es_AR')->getTitle());
        self::assertSame('{"type":"doc","content":[]}', $article->getTranslation('es_AR')->getContent());
    }

    public function testTheImageBelongsToTheArticleAndItsAltToTheLocale(): void
    {
        $article = new Article();
        $article->setCurrentLocale('es_AR');
        $article->setFallbackLocale('en_US');
        $image = new ArticleImage();

        $article->setImage($image);
        $article->setImageAlt('Una taza de café');

        self::assertSame($article, $image->getOwner());
        self::assertSame('Una taza de café', $article->getTranslation('es_AR')->getImageAlt());
    }

    private function author(string $code): Author
    {
        $author = new Author();
        $author->setCode($code);

        return $author;
    }

    private function byline(string $code, int $position): ArticleAuthor
    {
        $byline = new ArticleAuthor();
        $byline->setAuthor($this->author($code));
        $byline->setPosition($position);

        return $byline;
    }
}
