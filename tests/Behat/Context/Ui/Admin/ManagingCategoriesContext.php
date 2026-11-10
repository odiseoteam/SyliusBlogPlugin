<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform\ArticleCategoryContext;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory\CreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingCategoriesContext implements Context
{
    public function __construct(
        private readonly CurrentPageResolverInterface $currentPageResolver,
        private readonly CreatePageInterface $createPage,
        private readonly CreatePageInterface $createForParentPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly ArticleCategoryContext $categories,
        private readonly ObjectManager $manager,
    ) {
    }

    #[When('I want to create a new category')]
    public function iWantToCreateANewCategory(): void
    {
        $this->createPage->open();
    }

    #[When('I want to create a new category inside :category')]
    public function iWantToCreateANewCategoryInside(ArticleCategoryInterface $category): void
    {
        $this->createForParentPage->open(['id' => $category->getId()]);
    }

    #[When('I want to modify the :category category')]
    public function iWantToModifyTheCategory(ArticleCategoryInterface $category): void
    {
        $this->updatePage->open(['id' => $category->getId()]);
    }

    #[When('I specify its code as :code')]
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->resolveCreatePage()->specifyCode($code);
    }

    #[When('I name it :name in :localeCode')]
    public function iNameItIn(string $name, string $localeCode): void
    {
        $this->resolveCreatePage()->nameIt($name, $localeCode);
    }

    #[When('I set its slug to :slug in :localeCode')]
    public function iSetItsSlugToIn(string $slug, string $localeCode): void
    {
        $this->resolveCreatePage()->setSlug($slug, $localeCode);
    }

    #[When('I attach the :file image')]
    public function iAttachTheImage(string $file): void
    {
        $this->resolveCreatePage()->attachImage($file);
    }

    #[When('I add it')]
    #[When('I try to add it')]
    public function iAddIt(): void
    {
        $this->resolveCreatePage()->create();
    }

    #[When('I remove its image')]
    public function iRemoveItsImage(): void
    {
        $this->updatePage->removeImage();
    }

    #[When('I move the :category category down')]
    public function iMoveTheCategoryDown(ArticleCategoryInterface $category): void
    {
        $this->createPage->moveDown((string) $category->getName());
    }

    #[When('I delete the :category category')]
    public function iDeleteTheCategory(ArticleCategoryInterface $category): void
    {
        $this->createPage->open();
        $this->createPage->deleteCategory((string) $category->getName());
    }

    #[Then('the :name category should exist')]
    public function theCategoryShouldExist(string $name): void
    {
        Assert::notNull($this->find($name));
    }

    #[Then('the :name category should not exist')]
    public function theCategoryShouldNotExist(string $name): void
    {
        Assert::null($this->find($name));
    }

    #[Then('the :name category should be inside :parentName')]
    public function theCategoryShouldBeInside(string $name, string $parentName): void
    {
        Assert::same($this->find($name)?->getParent()?->getName(), $parentName);
    }

    #[Then('the :name category should have an image')]
    public function theCategoryShouldHaveAnImage(string $name): void
    {
        Assert::notNull($this->find($name)?->getImage()?->getPath());
    }

    #[Then('the :name category should have no image')]
    public function theCategoryShouldHaveNoImage(string $name): void
    {
        $category = $this->find($name);
        Assert::notNull($category);
        Assert::null($category->getImage());
    }

    #[Then('the tree should list :first and :second in this order')]
    #[Then('the tree should list :first, :second and :third in this order')]
    public function theTreeShouldListInThisOrder(string ...$names): void
    {
        Assert::same($this->createPage->getCategoriesInTree(), $names);
    }

    #[Then('I should be notified that the code is already used')]
    public function iShouldBeNotifiedThatTheCodeIsAlreadyUsed(): void
    {
        Assert::same($this->resolveCreatePage()->getCodeValidationMessage(), 'This code is already used.');
    }

    private function find(string $name): ?ArticleCategoryInterface
    {
        $this->manager->clear();

        return $this->categories->findByName($name);
    }

    private function resolveCreatePage(): CreatePageInterface
    {
        /** @var CreatePageInterface $page */
        $page = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->createForParentPage]);

        return $page;
    }
}
