<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Odiseo\BlogBundle\Model\ArticleCategoryInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory\CreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory\IndexPageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingArticleCategoriesContext implements Context
{
    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var IndexPageInterface */
    private $indexPage;

    /** @var CreatePageInterface */
    private $createPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    public function __construct(
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage
    ) {
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to add a new article category
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToAddNewArticleCategory(): void
    {
        $this->createPage->open(); // This method will send request.
    }

    /**
     * @When I fill the code with :articleCategoryCode
     * @param string $articleCategoryCode
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheCodeWith(string $articleCategoryCode): void
    {
        $this->createPage->fillCode($articleCategoryCode);
    }

    /**
     * @When I fill the slug with :articleCategorySlug
     * @param string $articleCategorySlug
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheSlugWith(string $articleCategorySlug)
    {
        $this->createPage->fillSlug($articleCategorySlug);
    }

    /**
     * @When I fill the title with :articleCategoryTitle
     * @When I rename it to :reportCategoryTitle
     * @param string $articleCategoryTitle
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheTitleWith(string $articleCategoryTitle): void
    {
        $this->createPage->fillTitle($articleCategoryTitle);
    }

    /**
     * @When I fill the content with :articleCategoryContent
     * @param string $articleCategoryContent
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheContentWithstring(string $articleCategoryContent): void
    {
        $this->createPage->fillContent($articleCategoryContent);
    }

    /**
     * @When I add it
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @Given /^I want to modify the (article category "([^"]+)")/
     * @param ArticleCategoryInterface $articleCategory
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToModifyArticleCategory(ArticleCategoryInterface $articleCategory): void
    {
        $this->updatePage->open(['id' => $articleCategory->getId()]);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I want to browse article categories
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToBrowseArticleCategories(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :numberOfArticleCategories article categories in the list
     * @param int $numberOfArticleCategories
     */
    public function iShouldSeeArticleCategoriesInTheList(int $numberOfArticleCategories = 1): void
    {
        Assert::same($this->indexPage->countItems(), (int) $numberOfArticleCategories);
    }

    /**
     * @Then /^the (article category "([^"]+)") should appear in the admin/
     * @param ArticleCategoryInterface $articleCategory
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function articleCategoryShouldAppearInTheAdmin(ArticleCategoryInterface $articleCategory): void
    {
        $this->indexPage->open();

        //Webmozart assert library.
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $articleCategory->getCode()]),
            sprintf('Article category %s should exist but it does not', $articleCategory->getCode())
        );
    }

    /**
     * @Then I should be notified that the form contains invalid fields
     */
    public function iShouldBeNotifiedThatTheFormContainsInvalidFields(): void
    {
        Assert::true($this->resolveCurrentPage()->containsError(),
            sprintf('The form should be notified you that the form contains invalid field but it does not')
        );
    }

    /**
     * @Then I should be notified that there is already an existing article category with provided code
     */
    public function iShouldBeNotifiedThatThereIsAlreadyAnExistingArticleCategoryWithCode(): void
    {
        Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(
            'There is an existing article category with this code.',
            false
        ));
    }

    /**
     * @return IndexPageInterface|CreatePageInterface|UpdatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->indexPage,
            $this->createPage,
            $this->updatePage,
        ]);
    }
}
