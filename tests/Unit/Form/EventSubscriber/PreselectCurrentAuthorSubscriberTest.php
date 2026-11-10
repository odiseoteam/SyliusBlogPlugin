<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Form\EventSubscriber;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\Author;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Odiseo\SyliusBlogPlugin\Form\EventSubscriber\PreselectCurrentAuthorSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[CoversClass(PreselectCurrentAuthorSubscriber::class)]
final class PreselectCurrentAuthorSubscriberTest extends TestCase
{
    private AdminUser $administrator;

    private Author $author;

    protected function setUp(): void
    {
        $this->administrator = new AdminUser();
        $this->author = new Author();
    }

    public function testANewArticleIsSignedByTheAuthorOfTheAdministrator(): void
    {
        $article = new Article();

        $this->preselect($article, $this->author);

        self::assertSame([$this->author], $article->getAuthors());
    }

    public function testWithoutAnAuthorTheArticleIsUnsigned(): void
    {
        $article = new Article();

        $this->preselect($article, null);

        self::assertSame([], $article->getAuthors());
    }

    public function testAStoredArticleIsLeftAsItIs(): void
    {
        $article = new Article();
        (new \ReflectionProperty(Article::class, 'id'))->setValue($article, 7);

        $this->preselect($article, $this->author);

        self::assertSame([], $article->getAuthors());
    }

    public function testAuthorsAlreadyChosenAreKept(): void
    {
        $other = new Author();
        $article = new Article();
        $article->setAuthors([$other]);

        $this->preselect($article, $this->author);

        self::assertSame([$other], $article->getAuthors());
    }

    private function preselect(Article $article, ?AuthorInterface $author): void
    {
        /** @var RepositoryInterface<AuthorInterface>&MockObject $repository */
        $repository = $this->createMock(RepositoryInterface::class);
        $repository
            ->method('findOneBy')
            ->with(['adminUser' => $this->administrator, 'enabled' => true])
            ->willReturn($author)
        ;

        $tokenStorage = new TokenStorage();
        $tokenStorage->setToken(new UsernamePasswordToken($this->administrator, 'admin', ['ROLE_ADMINISTRATION_ACCESS']));

        (new PreselectCurrentAuthorSubscriber($repository, $tokenStorage))->preselectAuthor(
            new FormEvent($this->createStub(FormInterface::class), $article),
        );
    }
}
