<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration;

use Odiseo\SyliusBlogPlugin\Entity\ArticleAuthorInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleCategoryRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleTagRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\AuthorRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\BlogChannelConfigurationRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ResourcesTest extends KernelTestCase
{
    /** @param class-string $repositoryInterface */
    #[DataProvider('repositories')]
    public function testEachResourceHasItsRepository(string $resource, string $repositoryInterface): void
    {
        self::assertInstanceOf($repositoryInterface, self::getContainer()->get('odiseo_blog.repository.' . $resource));
    }

    /** @return iterable<array{string, class-string}> */
    public static function repositories(): iterable
    {
        yield ['article', ArticleRepositoryInterface::class];
        yield ['article_category', ArticleCategoryRepositoryInterface::class];
        yield ['article_tag', ArticleTagRepositoryInterface::class];
        yield ['author', AuthorRepositoryInterface::class];
        yield ['channel_configuration', BlogChannelConfigurationRepositoryInterface::class];
    }

    public function testANewArticleIsReadyToTranslate(): void
    {
        $factory = self::getContainer()->get('odiseo_blog.factory.article');
        self::assertInstanceOf(FactoryInterface::class, $factory);

        $article = $factory->createNew();
        self::assertInstanceOf(ArticleInterface::class, $article);

        $article->setTitle('Hello');
        self::assertSame('en_US', $article->getTranslation()->getLocale());
    }

    public function testABylineHasItsFactory(): void
    {
        $factory = self::getContainer()->get('odiseo_blog.factory.article_author');
        self::assertInstanceOf(FactoryInterface::class, $factory);

        self::assertInstanceOf(ArticleAuthorInterface::class, $factory->createNew());
    }

    public function testANewChannelConfigurationStartsWithTheDefaults(): void
    {
        $factory = self::getContainer()->get('odiseo_blog.factory.channel_configuration');
        self::assertInstanceOf(FactoryInterface::class, $factory);

        $configuration = $factory->createNew();
        self::assertInstanceOf(BlogChannelConfigurationInterface::class, $configuration);

        self::assertTrue($configuration->isEnabled());
        self::assertSame(9, $configuration->getPostsPerPage());
        self::assertSame('en_US', $configuration->getTranslation()->getLocale());
    }
}
