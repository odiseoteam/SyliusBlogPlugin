<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\CreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\IndexPageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingArticlesContext implements Context
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

    /**
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     */
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
     * @Given I want to add a new article
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToAddNewArticle()
    {
        $this->createPage->open(); // This method will send request.
    }

    /**
     * @When I fill the code with :articleCode
     * @param $articleCode
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheCodeWith($articleCode)
    {
        $this->createPage->fillCode($articleCode);
    }

    /**
     * @When I fill the slug with :articleSlug
     * @param $articleSlug
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheSlugWith($articleSlug)
    {
        $this->createPage->fillSlug($articleSlug);
    }

    /**
     * @When I fill the title with :articleTitle
     * @When I rename it to :articleTitle
     * @param $articleTitle
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheTitleWith($articleTitle)
    {
        $this->createPage->fillTitle($articleTitle);
    }

    /**
     * @When I fill the content with :articleContent
     * @param $articleContent
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheContentWith($articleContent)
    {
        $this->createPage->fillContent($articleContent);
    }

    /**
     * @When I add it
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Given /^I want to modify the (article "([^"]+)")/
     * @param ArticleInterface $article
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToModifyArticle(ArticleInterface $article)
    {
        $this->updatePage->open(['id' => $article->getId()]);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I want to browse articles
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToBrowseArticles()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :numberOfArticles articles in the list
     * @param $numberOfArticles
     */
    public function iShouldSeeArticlesInTheList(int $numberOfArticles = 1): void
    {
        Assert::same($this->indexPage->countItems(), (int) $numberOfArticles);
    }

    /**
     * @Then /^the (article "([^"]+)") should appear in the admin/
     * @param ArticleInterface $article
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function articleShouldAppearInTheAdmin(ArticleInterface $article)
    {
        $this->indexPage->open();

        //Webmozart assert library.
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $article->getCode()]),
            sprintf('Article %s should exist but it does not', $article->getCode())
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
     * @Then I should be notified that there is already an existing article with provided code
     */
    public function iShouldBeNotifiedThatThereIsAlreadyAnExistingArticleWithCode(): void
    {
        Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(
            'There is an existing article with this code.',
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
