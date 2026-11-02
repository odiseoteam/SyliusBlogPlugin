<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleImage;
use Odiseo\SyliusBlogPlugin\Entity\Author;
use Odiseo\SyliusBlogPlugin\Twig\ImageRuntime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

#[CoversClass(ImageRuntime::class)]
final class ImageRuntimeTest extends TestCase
{
    public function testTheUrlOfAStoredImageGoesThroughTheFilter(): void
    {
        $cacheManager = $this->createMock(CacheManager::class);
        $cacheManager->method('getBrowserPath')->with('ab/cd/image.jpg', 'odiseo_blog_card')
            ->willReturn('https://example.com/media/cache/odiseo_blog_card/ab/cd/image.jpg');
        $image = new ArticleImage();
        $image->setPath('ab/cd/image.jpg');
        $article = $this->article();
        $article->setImage($image);

        self::assertSame(
            'https://example.com/media/cache/odiseo_blog_card/ab/cd/image.jpg',
            $this->runtime($cacheManager)->url($article, 'odiseo_blog_card'),
        );
    }

    public function testWithoutImageItFallsBackToAPlaceholder(): void
    {
        $runtime = $this->runtime();

        self::assertSame('/' . ImageRuntime::PLACEHOLDER, $runtime->url($this->article(), 'odiseo_blog_card'));
        self::assertSame('/' . ImageRuntime::PLACEHOLDER, $runtime->url(new ArticleCategory(), 'odiseo_blog_card'));
        self::assertSame('/' . ImageRuntime::AVATAR_PLACEHOLDER, $runtime->url(new Author(), 'odiseo_blog_avatar'));
    }

    public function testTheAltOfAnArticleFallsBackToItsTitle(): void
    {
        $article = $this->article();
        $article->setTitle('Brewing guide');

        self::assertSame('Brewing guide', $this->runtime()->alt($article));

        $article->setImageAlt('A cup of coffee');
        self::assertSame('A cup of coffee', $this->runtime()->alt($article));
    }

    public function testCategoriesAndAuthorsUseTheirName(): void
    {
        $category = new ArticleCategory();
        $category->setCurrentLocale('en_US');
        $category->setFallbackLocale('en_US');
        $category->setName('Recipes');
        $author = new Author();
        $author->setName('Jane Doe');

        self::assertSame('Recipes', $this->runtime()->alt($category));
        self::assertSame('Jane Doe', $this->runtime()->alt($author));
    }

    private function runtime(?CacheManager $cacheManager = null): ImageRuntime
    {
        return new ImageRuntime(
            $cacheManager ?? $this->createStub(CacheManager::class),
            new Packages(new PathPackage('/', new EmptyVersionStrategy())),
        );
    }

    private function article(): Article
    {
        $article = new Article();
        $article->setCurrentLocale('en_US');
        $article->setFallbackLocale('en_US');

        return $article;
    }
}
