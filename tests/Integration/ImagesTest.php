<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration;

use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleImage;
use Odiseo\SyliusBlogPlugin\Twig\ImageRuntime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class ImagesTest extends KernelTestCase
{
    public function testTheFilterSetsAreRegistered(): void
    {
        $configuration = self::getContainer()->get('liip_imagine.filter.configuration');
        self::assertInstanceOf(FilterConfiguration::class, $configuration);

        $sizes = [];
        foreach (['odiseo_blog_admin_thumbnail', 'odiseo_blog_avatar', 'odiseo_blog_card', 'odiseo_blog_hero', 'odiseo_blog_og'] as $name) {
            /** @var array{filters: array{fixed: array{width: int, height: int}}} $filterSet */
            $filterSet = $configuration->get($name);
            $sizes[$name] = [$filterSet['filters']['fixed']['width'], $filterSet['filters']['fixed']['height']];
        }

        self::assertSame([
            'odiseo_blog_admin_thumbnail' => [200, 200],
            'odiseo_blog_avatar' => [240, 240],
            'odiseo_blog_card' => [800, 450],
            'odiseo_blog_hero' => [1600, 900],
            'odiseo_blog_og' => [1200, 630],
        ], $sizes);
        self::assertSame('jpg', $configuration->get('odiseo_blog_og')['format']);
        self::assertSame('webp', $configuration->get('odiseo_blog_card')['format']);
    }

    public function testTemplatesGetTheImageUrlAndAlt(): void
    {
        $twig = self::getContainer()->get('twig');
        self::assertInstanceOf(Environment::class, $twig);
        $template = $twig->createTemplate("{{ odiseo_blog_image_url(article, 'odiseo_blog_card') }}|{{ odiseo_blog_image_alt(article) }}");

        $article = new Article();
        $article->setCurrentLocale('en_US');
        $article->setFallbackLocale('en_US');
        $article->setTitle('Brewing guide');
        self::assertSame('/' . ImageRuntime::PLACEHOLDER . '|Brewing guide', $template->render(['article' => $article]));

        $image = new ArticleImage();
        $image->setPath('ab/cd/post.jpg');
        $article->setImage($image);
        self::assertStringEndsWith('/media/cache/resolve/odiseo_blog_card/ab/cd/post.jpg|Brewing guide', $template->render(['article' => $article]));
    }
}
