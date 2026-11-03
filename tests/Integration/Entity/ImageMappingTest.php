<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryImage;
use Odiseo\SyliusBlogPlugin\Entity\ArticleImage;
use Odiseo\SyliusBlogPlugin\Entity\Author;
use Odiseo\SyliusBlogPlugin\Entity\AuthorImage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\File;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ImageMappingTest extends DatabaseTestCase
{
    public function testAnArticleIsStoredWithItsImageAndAlt(): void
    {
        $article = $this->article('post');
        $article->setImage($this->image(new ArticleImage(), 'ab/cd/post.jpg'));
        $article->setImageAlt('A cup of coffee');
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article = $this->reload(Article::class, $article->getId());

        self::assertSame('ab/cd/post.jpg', $article->getImage()?->getPath());
        self::assertSame($article, $article->getImage()->getOwner());
        self::assertSame('A cup of coffee', $article->getTranslation('en_US')->getImageAlt());
    }

    public function testReplacingOrDeletingTheOwnerDeletesTheImage(): void
    {
        $old = $this->image(new ArticleImage(), 'ab/cd/old.jpg');
        $article = $this->article('post');
        $article->setImage($old);
        $this->entityManager->persist($article);
        $this->entityManager->flush();
        $oldId = $old->getId();

        $new = $this->image(new ArticleImage(), 'ab/cd/new.jpg');
        $article->setImage($new);
        $this->entityManager->flush();
        $newId = $new->getId();
        self::assertNull($this->entityManager->find(ArticleImage::class, $oldId));

        $this->entityManager->remove($article);
        $this->entityManager->flush();
        self::assertNull($this->entityManager->find(ArticleImage::class, $newId));
    }

    public function testCategoriesAndAuthorsHaveAnImage(): void
    {
        $category = $this->category('news');
        $category->setImage($this->image(new ArticleCategoryImage(), 'ab/cd/news.jpg'));
        $author = $this->author('jane');
        $author->setImage($this->image(new AuthorImage(), 'ab/cd/jane.jpg'));
        $this->entityManager->persist($category);
        $this->entityManager->persist($author);
        $this->entityManager->flush();

        self::assertSame('ab/cd/news.jpg', $this->reload(ArticleCategory::class, $category->getId())->getImage()?->getPath());
        self::assertSame('ab/cd/jane.jpg', $this->reload(Author::class, $author->getId())->getImage()?->getPath());
    }

    public function testTheFileIsStoredOnSaveAndDeletedWithTheImage(): void
    {
        $image = new ArticleImage();
        $image->setFile(new File(\dirname(__DIR__, 3) . '/fixtures/article/images/01.jpg'));
        $article = $this->article('post');
        $article->setImage($image);

        $dispatcher = self::getContainer()->get('event_dispatcher');
        self::assertInstanceOf(EventDispatcherInterface::class, $dispatcher);
        $dispatcher->dispatch(new GenericEvent($article), 'odiseo_blog.article.pre_create');
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $imagesDir = self::getContainer()->getParameter('sylius_core.images_dir');
        self::assertIsString($imagesDir);
        $file = $imagesDir . '/' . $image->getPath();
        self::assertFileExists($file);

        $this->entityManager->remove($article);
        $this->entityManager->flush();

        self::assertFileDoesNotExist($file);
    }

    /**
     * @template T of ArticleImage|ArticleCategoryImage|AuthorImage
     *
     * @param T $image
     *
     * @return T
     */
    private function image(object $image, string $path): object
    {
        $image->setPath($path);

        return $image;
    }
}
