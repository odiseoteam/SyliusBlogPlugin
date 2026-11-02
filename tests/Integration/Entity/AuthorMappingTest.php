<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Entity;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Odiseo\SyliusBlogPlugin\Entity\ArticleAuthor;
use Odiseo\SyliusBlogPlugin\Entity\Author;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class AuthorMappingTest extends DatabaseTestCase
{
    public function testAnAuthorIsStoredWithItsAdminAndLinks(): void
    {
        $author = $this->author('jane');
        $author->setAdminUser($this->adminUser('jane'));
        $author->setSocialLinks([['network' => 'linkedin', 'url' => 'https://www.linkedin.com/in/jane']]);
        $author->setJobTitle('Editor');
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $author = $this->reload(Author::class, $author->getId());

        self::assertSame('jane', $author->getAdminUser()?->getUsername());
        // MySQL reorders the keys of a JSON column.
        self::assertEquals([['network' => 'linkedin', 'url' => 'https://www.linkedin.com/in/jane']], $author->getSocialLinks());
        self::assertSame('Editor', $author->getTranslation('en_US')->getJobTitle());
        self::assertTrue($author->isEnabled());
    }

    public function testDeletingTheAdminKeepsTheAuthor(): void
    {
        $adminUser = $this->adminUser('jane');
        $author = $this->author('jane');
        $author->setAdminUser($adminUser);
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        $this->entityManager->remove($adminUser);
        $this->entityManager->flush();

        self::assertNull($this->reload(Author::class, $author->getId())->getAdminUser());
    }

    public function testAnAuthorWhoSignsAnArticleCannotBeDeleted(): void
    {
        $author = $this->author('jane');
        $byline = new ArticleAuthor();
        $byline->setAuthor($author);
        $article = $this->article('post');
        $article->addArticleAuthor($byline);
        $this->entityManager->persist($author);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $this->expectException(ForeignKeyConstraintViolationException::class);

        $this->entityManager->remove($author);
        $this->entityManager->flush();
    }
}
