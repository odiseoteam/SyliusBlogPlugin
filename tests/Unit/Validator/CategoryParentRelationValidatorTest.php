<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Validator;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Validator\Constraints\CategoryParentRelation;
use Odiseo\SyliusBlogPlugin\Validator\Constraints\CategoryParentRelationValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/** @extends ConstraintValidatorTestCase<CategoryParentRelationValidator> */
#[CoversClass(CategoryParentRelationValidator::class)]
final class CategoryParentRelationValidatorTest extends ConstraintValidatorTestCase
{
    public function testARootCategoryIsValid(): void
    {
        $this->validator->validate(new ArticleCategory(), new CategoryParentRelation());

        $this->assertNoViolation();
    }

    public function testACategoryCanHaveAnotherCategoryAsParent(): void
    {
        $category = new ArticleCategory();
        $category->setParent(new ArticleCategory());

        $this->validator->validate($category, new CategoryParentRelation());

        $this->assertNoViolation();
    }

    public function testACategoryCannotBeItsOwnParent(): void
    {
        $category = new ArticleCategory();
        $category->setParent($category);

        $this->validator->validate($category, new CategoryParentRelation());

        $this->buildViolation('odiseo_blog.article_category.parent.invalid_relation')
            ->atPath('property.path.parent')
            ->assertRaised()
        ;
    }

    public function testACategoryCannotBeTheChildOfADescendant(): void
    {
        $category = new ArticleCategory();
        $child = new ArticleCategory();
        $grandchild = new ArticleCategory();
        $child->setParent($category);
        $grandchild->setParent($child);

        $category->setParent($grandchild);

        $this->validator->validate($category, new CategoryParentRelation());

        $this->buildViolation('odiseo_blog.article_category.parent.invalid_relation')
            ->atPath('property.path.parent')
            ->assertRaised()
        ;
    }

    public function testItOnlyValidatesCategories(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new Article(), new CategoryParentRelation());
    }

    protected function createValidator(): CategoryParentRelationValidator
    {
        return new CategoryParentRelationValidator();
    }
}
