<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ArticleCategoryTreeTest extends DatabaseTestCase
{
    public function testTheTreeKeepsItsBoundsAndSiblingOrder(): void
    {
        $root = $this->category('root');
        $this->entityManager->persist($root);
        $this->entityManager->persist($this->category('first', $root));
        $this->entityManager->persist($this->category('second', $root));
        $this->entityManager->flush();

        $root = $this->reload(ArticleCategory::class, $root->getId());

        self::assertSame([1, 6, 0], [$root->getLeft(), $root->getRight(), $root->getLevel()]);
        self::assertSame(['first', 'second'], $this->childCodes($root));
        foreach ($root->getChildren() as $position => $child) {
            self::assertSame($position, $child->getPosition());
            self::assertSame(1, $child->getLevel());
            self::assertSame($root->getId(), $child->getRoot()?->getId());
        }
    }

    public function testMovingACategoryReordersItsSiblings(): void
    {
        $root = $this->category('root');
        $second = $this->category('second', $root);
        $this->entityManager->persist($root);
        $this->entityManager->persist($this->category('first', $root));
        $this->entityManager->persist($second);
        $this->entityManager->flush();

        $second->setPosition(0);
        $this->entityManager->flush();

        self::assertSame(['second', 'first'], $this->childCodes($this->reload(ArticleCategory::class, $root->getId())));
    }

    public function testDeletingACategoryUnlinksItsArticles(): void
    {
        $news = $this->category('news');
        $article = $this->article('post');
        $article->addCategory($news);
        $article->setMainCategory($news);
        $this->entityManager->persist($news);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $this->entityManager->remove($news);
        $this->entityManager->flush();

        $article = $this->reload(Article::class, $article->getId());
        self::assertNull($article->getMainCategory());
        self::assertCount(0, $article->getCategories());
    }

    /** @return list<string|null> */
    private function childCodes(ArticleCategoryInterface $category): array
    {
        return $category->getChildren()->map(static fn (ArticleCategoryInterface $child) => $child->getCode())->getValues();
    }
}
