<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Factory;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Factory\ArticleCategoryFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Factory\FactoryInterface;

#[CoversClass(ArticleCategoryFactory::class)]
final class ArticleCategoryFactoryTest extends TestCase
{
    public function testItCreatesACategoryInsideAParent(): void
    {
        $parent = new ArticleCategory();

        $category = $this->factory()->createForParent($parent);

        self::assertSame($parent, $category->getParent());
        self::assertTrue($parent->hasChild($category));
    }

    public function testANewCategoryIsARoot(): void
    {
        self::assertTrue($this->factory()->createNew()->isRoot());
    }

    private function factory(): ArticleCategoryFactory
    {
        /** @var FactoryInterface<ArticleCategoryInterface>&Stub $decoratedFactory */
        $decoratedFactory = $this->createStub(FactoryInterface::class);
        $decoratedFactory->method('createNew')->willReturnCallback(static fn () => new ArticleCategory());

        return new ArticleCategoryFactory($decoratedFactory);
    }
}
