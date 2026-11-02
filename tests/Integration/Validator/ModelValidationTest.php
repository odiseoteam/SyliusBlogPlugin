<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Validator;

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
