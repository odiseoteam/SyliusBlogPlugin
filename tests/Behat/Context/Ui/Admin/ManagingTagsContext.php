<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleTag\CreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleTag\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingTagsContext implements Context
{
    public function __construct(
        private readonly CurrentPageResolverInterface $currentPageResolver,
        private readonly IndexPageInterface $indexPage,
        private readonly CreatePageInterface $createPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly ObjectManager $manager,
    ) {
    }

    #[When('I want to create a new tag')]
    public function iWantToCreateANewTag(): void
    {
        $this->createPage->open();
    }

    #[When('I want to modify the :tag tag')]
    public function iWantToModifyTheTag(ArticleTagInterface $tag): void
    {
        $this->updatePage->open(['id' => $tag->getId()]);
    }

    #[When('I specify its code as :code')]
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->createPage->specifyCode($code);
    }

    #[When('I name it :name in :localeCode')]
    public function iNameItIn(string $name, string $localeCode): void
    {
        $this->resolveCurrentPage()->nameIt($name, $localeCode);
    }

    #[When('I set its slug to :slug in :localeCode')]
    public function iSetItsSlugToIn(string $slug, string $localeCode): void
    {
        $this->createPage->setSlug($slug, $localeCode);
    }

    #[When('I generate its slug in :localeCode')]
    public function iGenerateItsSlugIn(string $localeCode): void
    {
        $this->createPage->generateSlug($localeCode);
    }

    #[When('I add it')]
    #[When('I try to add it')]
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    #[When('I save my changes')]
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    #[When('I delete the :tag tag')]
    public function iDeleteTheTag(ArticleTagInterface $tag): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $tag->getName()]);
    }

    #[Then('the tag :name should appear in the list')]
    public function theTagShouldAppearInTheList(string $name): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    #[Then('the tag :name should not appear in the list')]
    public function theTagShouldNotAppearInTheList(string $name): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    #[Then('the :tag tag should have the slug :slug in :localeCode')]
    public function theTagShouldHaveTheSlugIn(ArticleTagInterface $tag, string $slug, string $localeCode): void
    {
        $this->manager->refresh($tag);

        Assert::same($tag->getTranslation($localeCode)->getSlug(), $slug);
    }

    #[Then('I should not be able to edit its code')]
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    #[Then('I should be notified that the code is already used')]
    public function iShouldBeNotifiedThatTheCodeIsAlreadyUsed(): void
    {
        Assert::same($this->createPage->getCodeValidationMessage(), 'This code is already used.');
    }

    #[Then('I should be notified that the slug is already used in :localeCode')]
    public function iShouldBeNotifiedThatTheSlugIsAlreadyUsedIn(string $localeCode): void
    {
        Assert::same($this->createPage->getSlugValidationMessage($localeCode), 'This slug is already used.');
    }

    private function resolveCurrentPage(): CreatePageInterface|UpdatePageInterface
    {
        /** @var CreatePageInterface|UpdatePageInterface $page */
        $page = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        return $page;
    }
}
