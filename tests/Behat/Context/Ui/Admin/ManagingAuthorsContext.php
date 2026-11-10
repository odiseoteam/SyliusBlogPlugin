<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform\AuthorContext;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author\CreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingAuthorsContext implements Context
{
    public function __construct(
        private readonly CurrentPageResolverInterface $currentPageResolver,
        private readonly IndexPageInterface $indexPage,
        private readonly CreatePageInterface $createPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly AuthorContext $authors,
        private readonly ObjectManager $manager,
        private readonly NotificationCheckerInterface $notificationChecker,
    ) {
    }

    #[When('I want to create a new author')]
    public function iWantToCreateANewAuthor(): void
    {
        $this->createPage->open();
    }

    #[When('I want to modify the :author author')]
    public function iWantToModifyTheAuthor(AuthorInterface $author): void
    {
        $this->updatePage->open(['id' => $author->getId()]);
    }

    #[When('I specify its code as :code')]
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->createPage->specifyCode($code);
    }

    #[When('I name it :name')]
    public function iNameIt(string $name): void
    {
        $this->resolveCurrentPage()->nameIt($name);
    }

    #[When('I set its slug to :slug')]
    public function iSetItsSlugTo(string $slug): void
    {
        $this->createPage->setSlug($slug);
    }

    #[When('I generate its slug')]
    public function iGenerateItsSlug(): void
    {
        $this->createPage->generateSlug();
    }

    #[When('I attach the :file avatar')]
    public function iAttachTheAvatar(string $file): void
    {
        $this->createPage->attachAvatar($file);
    }

    #[When('I add a :network link to :url')]
    public function iAddALinkTo(string $network, string $url): void
    {
        $this->resolveCurrentPage()->addSocialLink($network, $url);
    }

    #[When('I remove its first link')]
    public function iRemoveItsFirstLink(): void
    {
        $this->updatePage->removeSocialLink(1);
    }

    #[When('I remove its avatar')]
    public function iRemoveItsAvatar(): void
    {
        $this->updatePage->removeAvatar();
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

    #[When('I delete the :author author')]
    public function iDeleteTheAuthor(AuthorInterface $author): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $author->getName()]);
    }

    #[Then('the author :name should have the slug :slug')]
    public function theAuthorShouldHaveTheSlug(string $name, string $slug): void
    {
        Assert::same($this->find($name)?->getSlug(), $slug);
    }

    #[Then('the author :name should have the links :first and :second in this order')]
    #[Then('the author :name should have the link :first')]
    public function theAuthorShouldHaveTheLinks(string $name, string ...$links): void
    {
        $stored = array_map(
            static fn (array $link): string => $link['network'] . ': ' . $link['url'],
            $this->find($name)?->getSocialLinks() ?? [],
        );

        Assert::same($stored, $links);
    }

    #[Then('the author :name should have an avatar')]
    public function theAuthorShouldHaveAnAvatar(string $name): void
    {
        Assert::notNull($this->find($name)?->getImage()?->getPath());
    }

    #[Then('the author :name should have no avatar')]
    public function theAuthorShouldHaveNoAvatar(string $name): void
    {
        $author = $this->find($name);
        Assert::notNull($author);
        Assert::null($author->getImage());
    }

    #[Then('the author :name should exist')]
    public function theAuthorShouldExist(string $name): void
    {
        Assert::notNull($this->find($name));
    }

    #[Then('the author :name should not exist')]
    public function theAuthorShouldNotExist(string $name): void
    {
        Assert::null($this->find($name));
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

    #[Then('I should be notified that the slug is already used')]
    public function iShouldBeNotifiedThatTheSlugIsAlreadyUsed(): void
    {
        Assert::same($this->createPage->getSlugValidationMessage(), 'This slug is already used.');
    }

    #[Then('I should be notified that the author signs articles')]
    public function iShouldBeNotifiedThatTheAuthorSignsArticles(): void
    {
        $this->notificationChecker->checkNotification('only be disabled', NotificationType::failure());
    }

    private function find(string $name): ?AuthorInterface
    {
        $this->manager->clear();

        return $this->authors->findByName($name);
    }

    private function resolveCurrentPage(): CreatePageInterface|UpdatePageInterface
    {
        /** @var CreatePageInterface|UpdatePageInterface $page */
        $page = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        return $page;
    }
}
