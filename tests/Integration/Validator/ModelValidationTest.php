<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Validator;

use Odiseo\SyliusBlogPlugin\Entity\ArticleImage;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfiguration;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ModelValidationTest extends DatabaseTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = self::getContainer()->get('validator');
        self::assertInstanceOf(ValidatorInterface::class, $validator);
        $this->validator = $validator;
    }

    public function testAValidArticleHasNoViolations(): void
    {
        self::assertSame([], $this->violations($this->article('post')));
    }

    public function testCodesAreUnique(): void
    {
        $this->entityManager->persist($this->article('post', 'first'));
        $this->entityManager->persist($this->tag('tips'));
        $this->entityManager->persist($this->author('jane'));
        $this->entityManager->flush();

        self::assertSame(['code' => 'odiseo_blog.code.unique'], $this->violations($this->article('post', 'second')));
        self::assertSame('odiseo_blog.code.unique', $this->violations($this->tag('tips'))['code'] ?? null);
        self::assertSame('odiseo_blog.code.unique', $this->violations($this->author('jane'))['code'] ?? null);
    }

    public function testCodesOnlyTakeLettersNumbersDashesAndUnderscores(): void
    {
        self::assertSame(['code' => 'odiseo_blog.code.regex'], $this->violations($this->article('my post')));
    }

    public function testSlugsAreUniquePerLocale(): void
    {
        $this->entityManager->persist($this->article('first', 'hello'));
        $this->entityManager->persist($this->category('news'));
        $this->entityManager->flush();

        self::assertSame(
            ['translations[en_US].slug' => 'odiseo_blog.slug.unique'],
            $this->violations($this->article('second', 'hello')),
        );
        self::assertSame([], $this->violations($this->article('third', 'hello', 'es_AR')));

        $category = $this->category('other');
        $category->setSlug('news');
        self::assertSame(['translations[en_US].slug' => 'odiseo_blog.slug.unique'], $this->violations($category));
    }

    public function testTheMainCategoryMustBeOneOfTheCategories(): void
    {
        $article = $this->article('post');
        $article->addCategory($this->category('news'));
        $article->setMainCategory($this->category('tips'));

        self::assertSame(
            ['mainCategory' => 'odiseo_blog.article.main_category.not_in_categories'],
            $this->violations($article),
        );
    }

    public function testImagesOnlyTakeTheImageTypesSyliusAllows(): void
    {
        $image = new ArticleImage();
        $image->setFile(new File(\dirname(__DIR__, 3) . '/public/images/placeholder.svg'));
        $article = $this->article('post');
        $article->setImage($image);

        self::assertSame('sylius.image.file.allowed_mime_types', $this->violations($article)['image.file'] ?? null);
    }

    public function testTheImageAltIsAtMost255Characters(): void
    {
        $article = $this->article('post');
        $article->setImageAlt(str_repeat('a', 256));

        self::assertArrayHasKey('translations[en_US].imageAlt', $this->violations($article));
    }

    public function testACategoryCannotBeItsOwnAncestor(): void
    {
        $parent = $this->category('parent_cat');
        $child = $this->category('child_cat', $parent);
        $this->entityManager->persist($parent);
        $this->entityManager->persist($child);
        $this->entityManager->flush();

        self::assertSame([], $this->violations($child));

        $parent->setParent($child);
        self::assertSame(
            ['parent' => 'odiseo_blog.article_category.parent.invalid_relation'],
            $this->violations($parent),
        );
    }

    public function testAnAdministratorIsTheAuthorOfOnePerson(): void
    {
        $adminUser = $this->adminUser('shared_admin');
        $first = $this->author('first_author');
        $first->setAdminUser($adminUser);
        $this->entityManager->persist($first);
        $this->entityManager->flush();

        $second = $this->author('second_author');
        $second->setAdminUser($adminUser);

        self::assertSame(['adminUser' => 'odiseo_blog.author.admin_user.unique'], $this->violations($second));
    }

    public function testTheLinksOfAnAuthorNeedANetworkAndAUrl(): void
    {
        $author = $this->author('linked');
        $author->setSocialLinks([['network' => 'github', 'url' => 'https://github.com/jane']]);
        self::assertSame([], $this->violations($author));

        $author->setSocialLinks([['network' => 'github', 'url' => 'not a url'], ['network' => '', 'url' => 'https://example.com']]);
        self::assertArrayHasKey('socialLinks[0][url]', $this->violations($author));
        self::assertArrayHasKey('socialLinks[1][network]', $this->violations($author));
    }

    public function testAPageHasBetweenOneAndAHundredArticles(): void
    {
        $configuration = new BlogChannelConfiguration();
        $configuration->setCurrentLocale('en_US');
        $configuration->setFallbackLocale('en_US');
        $configuration->setChannel($this->channel('WEB'));

        $configuration->setPostsPerPage(9);
        self::assertSame([], $this->violations($configuration));

        $configuration->setPostsPerPage(0);
        self::assertArrayHasKey('postsPerPage', $this->violations($configuration));

        $configuration->setPostsPerPage(101);
        self::assertArrayHasKey('postsPerPage', $this->violations($configuration));

        $configuration->setPostsPerPage(null);
        self::assertArrayHasKey('postsPerPage', $this->violations($configuration));
    }

    public function testTheBlogTitleIsAtMost255Characters(): void
    {
        $configuration = new BlogChannelConfiguration();
        $configuration->setCurrentLocale('en_US');
        $configuration->setFallbackLocale('en_US');
        $configuration->setChannel($this->channel('WEB'));
        $configuration->setPostsPerPage(9);
        $configuration->setTitle(str_repeat('a', 256));

        self::assertArrayHasKey('translations[en_US].title', $this->violations($configuration));
    }

    /** @return array<string, string> Message templates by property path. */
    private function violations(object $value): array
    {
        $violations = [];
        foreach ($this->validator->validate($value, null, ['odiseo_blog']) as $violation) {
            $violations[$violation->getPropertyPath()] = $violation->getMessageTemplate();
        }

        return $violations;
    }
}
