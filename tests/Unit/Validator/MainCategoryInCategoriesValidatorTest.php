<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Validator;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Validator\Constraints\MainCategoryInCategories;
use Odiseo\SyliusBlogPlugin\Validator\Constraints\MainCategoryInCategoriesValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<MainCategoryInCategoriesValidator> */
#[CoversClass(MainCategoryInCategoriesValidator::class)]
final class MainCategoryInCategoriesValidatorTest extends ConstraintValidatorTestCase
{
    public function testAnArticleWithoutMainCategoryIsValid(): void
    {
        $this->validator->validate(new Article(), new MainCategoryInCategories());

        $this->assertNoViolation();
    }

    public function testTheMainCategoryCanBeOneOfTheCategories(): void
    {
        $category = new ArticleCategory();
        $article = new Article();
        $article->addCategory($category);
        $article->setMainCategory($category);

        $this->validator->validate($article, new MainCategoryInCategories());

        $this->assertNoViolation();
    }

    public function testTheMainCategoryMustBeOneOfTheCategories(): void
    {
        $article = new Article();
        $article->addCategory(new ArticleCategory());
        $article->setMainCategory(new ArticleCategory());

        $this->validator->validate($article, new MainCategoryInCategories());

        $this->buildViolation('odiseo_blog.article.main_category.not_in_categories')
            ->atPath('property.path.mainCategory')
            ->assertRaised()
        ;
    }

    public function testItOnlyValidatesArticles(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new ArticleCategory(), new MainCategoryInCategories());
    }

    protected function createValidator(): MainCategoryInCategoriesValidator
    {
        return new MainCategoryInCategoriesValidator();
    }
}
