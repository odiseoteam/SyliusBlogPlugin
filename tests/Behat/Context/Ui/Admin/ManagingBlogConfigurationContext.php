<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Odiseo\SyliusBlogPlugin\Repository\BlogChannelConfigurationRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ChannelConfiguration\IndexPageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ChannelConfiguration\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingBlogConfigurationContext implements Context
{
    /** @param BlogChannelConfigurationRepositoryInterface<BlogChannelConfigurationInterface> $configurationRepository */
    public function __construct(
        private readonly IndexPageInterface $indexPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly BlogChannelConfigurationRepositoryInterface $configurationRepository,
        private readonly ObjectManager $manager,
    ) {
    }

    #[When('I browse the blog configuration')]
    public function iBrowseTheBlogConfiguration(): void
    {
        $this->indexPage->open();
    }

    #[When('I want to configure the blog of the :channel channel')]
    public function iWantToConfigureTheBlogOfTheChannel(ChannelInterface $channel): void
    {
        $this->updatePage->open(['channelCode' => $channel->getCode()]);
    }

    #[When('I set it to list :postsPerPage articles per page')]
    public function iSetItToListArticlesPerPage(int $postsPerPage): void
    {
        $this->updatePage->setPostsPerPage($postsPerPage);
    }

    #[When('I name the blog :title in :localeCode')]
    public function iNameTheBlogIn(string $title, string $localeCode): void
    {
        $this->updatePage->nameBlog($title, $localeCode);
    }

    #[When('I hide the blog')]
    public function iHideTheBlog(): void
    {
        $this->updatePage->hideBlog();
    }

    #[When('I save my changes')]
    #[When('I try to save my changes')]
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    #[Then('I should see :count channels in the list')]
    public function iShouldSeeChannelsInTheList(int $count): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    #[Then('the :channel channel should show its blog with :postsPerPage articles per page')]
    public function theChannelShouldShowItsBlogWithArticlesPerPage(ChannelInterface $channel, int $postsPerPage): void
    {
        Assert::true($this->indexPage->isBlogShownIn((string) $channel->getName()));
        Assert::same($this->indexPage->getPostsPerPageOf((string) $channel->getName()), $postsPerPage);
    }

    #[Then('the blog should be shown')]
    public function theBlogShouldBeShown(): void
    {
        Assert::true($this->updatePage->isBlogShown());
    }

    #[Then('it should list :postsPerPage articles per page')]
    public function itShouldListArticlesPerPage(int $postsPerPage): void
    {
        Assert::same($this->updatePage->getPostsPerPage(), $postsPerPage);
    }

    #[Then('the blog of the :channel channel should list :postsPerPage articles per page')]
    public function theBlogOfTheChannelShouldListArticlesPerPage(ChannelInterface $channel, int $postsPerPage): void
    {
        Assert::same($this->storedConfigurationOf($channel)->getPostsPerPage(), $postsPerPage);
    }

    #[Then('the blog of the :channel channel should be named :title in :localeCode')]
    public function theBlogOfTheChannelShouldBeNamedIn(ChannelInterface $channel, string $title, string $localeCode): void
    {
        Assert::same($this->storedConfigurationOf($channel)->getTranslation($localeCode)->getTitle(), $title);
    }

    #[Then('the blog of the :channel channel should be hidden')]
    public function theBlogOfTheChannelShouldBeHidden(ChannelInterface $channel): void
    {
        Assert::false($this->storedConfigurationOf($channel)->isEnabled());
    }

    #[Then('I should be notified that the articles per page must be between 1 and 100')]
    public function iShouldBeNotifiedThatTheArticlesPerPageMustBeBetween(): void
    {
        Assert::same($this->updatePage->getPostsPerPageValidationMessage(), 'This value should be between 1 and 100.');
    }

    #[Then('the :channel channel should have no blog configuration stored')]
    public function theChannelShouldHaveNoBlogConfigurationStored(ChannelInterface $channel): void
    {
        $this->manager->clear();

        Assert::null($this->configurationRepository->findOneByChannel($channel));
    }

    private function storedConfigurationOf(ChannelInterface $channel): BlogChannelConfigurationInterface
    {
        $this->manager->clear();

        $configuration = $this->configurationRepository->findOneByChannel($channel);
        Assert::notNull($configuration);

        return $configuration;
    }
}
