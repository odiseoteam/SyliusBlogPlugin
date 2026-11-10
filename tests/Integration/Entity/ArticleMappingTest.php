<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleAuthor;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ArticleMappingTest extends DatabaseTestCase
{
    public function testAnArticleIsStoredWithItsRelations(): void
    {
        $news = $this->category('news');
        $tag = $this->tag('tips');
        $article = $this->article('first-post', 'first-post');
        $article->addChannel($this->channel('WEB'));
        $article->addCategory($news);
        $article->setMainCategory($news);
        $article->addTag($tag);
        foreach (['jane', 'john'] as $position => $code) {
            $byline = new ArticleAuthor();
            $byline->setAuthor($this->author($code));
            $byline->setPosition($position);
            $article->addArticleAuthor($byline);
            $this->entityManager->persist($byline->getAuthor() ?? throw new \LogicException());
        }
        $this->entityManager->persist($news);
        $this->entityManager->persist($tag);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article = $this->reload(Article::class, $article->getId());
        $article->setCurrentLocale('en_US');

        self::assertSame(ArticleInterface::STATE_DRAFT, $article->getState());
        self::assertNotNull($article->getCreatedAt());
        self::assertSame('first-post', $article->getSlug());
        self::assertSame(['WEB'], $article->getChannels()->map(static fn ($channel) => $channel->getCode())->getValues());
        self::assertSame('news', $article->getMainCategory()?->getCode());
        self::assertSame(['news'], $article->getCategories()->map(static fn ($category) => $category->getCode())->getValues());
        self::assertSame(['tips'], $article->getTags()->map(static fn ($tag) => $tag->getCode())->getValues());
        self::assertSame(['jane', 'john'], array_map(static fn ($author) => $author->getCode(), $article->getAuthors()));
    }

    public function testTheContentIsStoredExactlyAsSent(): void
    {
        // Key order and the empty object would not survive a JSON column decoded into PHP arrays.
        $content = '{"type":"doc","content":[{"type":"paragraph","attrs":{},"content":[{"text":"Hi","type":"text"}]}]}';
        $article = $this->article('content');
        $article->setContent($content);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article = $this->reload(Article::class, $article->getId());

        self::assertSame($content, $article->getTranslation('en_US')->getContent());
    }

    public function testReorderingTheAuthorsKeepsTheirBylines(): void
    {
        $jane = $this->author('order_jane');
        $john = $this->author('order_john');
        $article = $this->article('reorder');
        $article->setAuthors([$jane, $john]);
        $this->entityManager->persist($jane);
        $this->entityManager->persist($john);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article->setAuthors([$john, $jane]);
        $this->entityManager->flush();
        $article = $this->reload(Article::class, $article->getId());

        self::assertSame(['order_john', 'order_jane'], array_map(static fn ($author) => $author->getCode(), $article->getAuthors()));
    }

    public function testRemovingABylineDeletesIt(): void
    {
        $byline = new ArticleAuthor();
        $byline->setAuthor($this->author('jane'));
        $article = $this->article('byline');
        $article->addArticleAuthor($byline);
        $this->entityManager->persist($byline->getAuthor() ?? throw new \LogicException());
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article->removeArticleAuthor($byline);
        $this->entityManager->flush();

        self::assertSame(0, $this->entityManager->getRepository(ArticleAuthor::class)->count(['article' => $article]));
    }
}
