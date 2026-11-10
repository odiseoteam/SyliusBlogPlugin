<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Twig\Component\Admin;

use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleCategoryRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Twig\Component\Admin\ArticleCategoryTreeComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;

#[CoversClass(ArticleCategoryTreeComponent::class)]
final class ArticleCategoryTreeComponentTest extends TestCase
{
    public function testItNestsEachCategoryUnderItsParent(): void
    {
        $repository = $this->repository([
            ['id' => 3, 'parentId' => 1, 'name' => 'Brewing'],
            ['id' => 1, 'parentId' => null, 'name' => 'Coffee'],
            ['id' => 2, 'parentId' => null, 'name' => 'Tea'],
        ]);

        $tree = $this->component($repository)->getTree();

        self::assertSame(['Coffee', 'Tea'], array_column($tree, 'name'));
        self::assertSame(['Brewing'], array_column($tree[0]['children'], 'name'));
        self::assertSame([], $tree[1]['children']);
    }

    public function testItFallsBackToTheDefaultLocaleWithoutACurrentOne(): void
    {
        $repository = $this->createMock(ArticleCategoryRepositoryInterface::class);
        $repository->expects(self::once())->method('findAllForTree')->with('en_US', 'en_US')->willReturn([]);

        $localeContext = $this->createStub(LocaleContextInterface::class);
        $localeContext->method('getLocaleCode')->willThrowException(new LocaleNotFoundException());

        $this->component($repository, localeContext: $localeContext)->getTree();
    }

    public function testMovingUpLowersThePosition(): void
    {
        $category = new ArticleCategory();
        $category->setPosition(2);
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $this->component($this->repository([], $category), $manager)->moveUp(1);

        self::assertSame(1, $category->getPosition());
    }

    public function testTheFirstCategoryCannotMoveUp(): void
    {
        $category = new ArticleCategory();
        $category->setPosition(0);
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::never())->method('flush');

        $this->component($this->repository([], $category), $manager)->moveUp(1);

        self::assertSame(0, $category->getPosition());
    }

    public function testMovingDownRaisesThePosition(): void
    {
        $category = new ArticleCategory();
        $category->setPosition(0);
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $this->component($this->repository([], $category), $manager)->moveDown(1);

        self::assertSame(1, $category->getPosition());
    }

    public function testAnUnknownCategoryIsIgnored(): void
    {
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::never())->method('flush');

        $component = $this->component($this->repository([], null), $manager);
        $component->moveUp(9);
        $component->moveDown(9);
    }

    /**
     * @param list<array{id: int, parentId: int|null, name: string}> $rows
     *
     * @return ArticleCategoryRepositoryInterface<ArticleCategoryInterface>&Stub
     */
    private function repository(array $rows, ?ArticleCategoryInterface $category = null): ArticleCategoryRepositoryInterface
    {
        /** @var ArticleCategoryRepositoryInterface<ArticleCategoryInterface>&Stub $repository */
        $repository = $this->createStub(ArticleCategoryRepositoryInterface::class);
        $repository->method('findAllForTree')->willReturn($rows);
        $repository->method('find')->willReturn($category);

        return $repository;
    }

    /** @param ArticleCategoryRepositoryInterface<ArticleCategoryInterface> $repository */
    private function component(
        ArticleCategoryRepositoryInterface $repository,
        ?ObjectManager $manager = null,
        ?LocaleContextInterface $localeContext = null,
    ): ArticleCategoryTreeComponent {
        $localeContext ??= $this->createStub(LocaleContextInterface::class);
        $localeProvider = $this->createStub(TranslationLocaleProviderInterface::class);
        $localeProvider->method('getDefaultLocaleCode')->willReturn('en_US');

        return new ArticleCategoryTreeComponent(
            $repository,
            $manager ?? $this->createStub(ObjectManager::class),
            $localeContext,
            $localeProvider,
        );
    }
}
