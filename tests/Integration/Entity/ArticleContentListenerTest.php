<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTranslationInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ArticleContentListenerTest extends DatabaseTestCase
{
    private const HELLO = '{"type":"doc","content":[{"type":"heading","attrs":{"level":2},"content":[{"type":"text","text":"Brewing guide"}]},{"type":"paragraph","content":[{"type":"text","text":"Grind, pour and wait."}]}]}';

    public function testANewArticleSavesTheTextAndTheWordsOfItsContent(): void
    {
        $article = $this->article('new_content');
        $article->setContent(self::HELLO);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $translation = $this->translation($this->reload(Article::class, $article->getId()));

        self::assertSame("Brewing guide\n\nGrind, pour and wait.", $translation->getPlainText());
        self::assertSame(6, $translation->getWordCount());
    }

    public function testContentSetAfterPersistingIsCounted(): void
    {
        $article = $this->article('late_content');
        $this->entityManager->persist($article);
        $article->setContent(self::HELLO);
        $this->entityManager->flush();

        self::assertSame(6, $this->translation($this->reload(Article::class, $article->getId()))->getWordCount());
    }

    public function testChangingTheContentUpdatesThem(): void
    {
        $article = $this->article('edited_content');
        $article->setContent(self::HELLO);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article->setContent('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Shorter."}]}]}');
        $this->entityManager->flush();
        $translation = $this->translation($this->reload(Article::class, $article->getId()));

        self::assertSame('Shorter.', $translation->getPlainText());
        self::assertSame(1, $translation->getWordCount());
    }

    public function testChangingSomethingElseKeepsThem(): void
    {
        $article = $this->article('other_change');
        $article->setContent(self::HELLO);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article->setTitle('Another title');
        $this->entityManager->flush();
        $translation = $this->translation($this->reload(Article::class, $article->getId()));

        self::assertSame(6, $translation->getWordCount());
    }

    public function testAnArticleWithoutContentHasNoText(): void
    {
        $article = $this->article('no_content');
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $translation = $this->translation($this->reload(Article::class, $article->getId()));

        self::assertNull($translation->getPlainText());
        self::assertSame(0, $translation->getWordCount());
    }

    public function testRemovingTheContentClearsThem(): void
    {
        $article = $this->article('removed_content');
        $article->setContent(self::HELLO);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article->setContent(null);
        $this->entityManager->flush();
        $translation = $this->translation($this->reload(Article::class, $article->getId()));

        self::assertNull($translation->getPlainText());
        self::assertSame(0, $translation->getWordCount());
    }

    private function translation(Article $article): ArticleTranslationInterface
    {
        $translation = $article->getTranslation('en_US');
        self::assertInstanceOf(ArticleTranslationInterface::class, $translation);

        return $translation;
    }
}
