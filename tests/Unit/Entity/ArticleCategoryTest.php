<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Entity;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryImage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArticleCategory::class)]
final class ArticleCategoryTest extends TestCase
{
    public function testParentAndChildrenStayInSync(): void
    {
        $parent = new ArticleCategory();
        $child = new ArticleCategory();

        $child->setParent($parent);
        self::assertTrue($parent->hasChild($child));
        self::assertFalse($child->isRoot());

        $parent->removeChild($child);
        self::assertNull($child->getParent());
        self::assertFalse($parent->hasChildren());
        self::assertTrue($child->isRoot());
    }

    public function testTheImageBelongsToTheCategory(): void
    {
        $category = new ArticleCategory();
        $image = new ArticleCategoryImage();

        $category->setImage($image);

        self::assertSame($category, $image->getOwner());
    }
}
