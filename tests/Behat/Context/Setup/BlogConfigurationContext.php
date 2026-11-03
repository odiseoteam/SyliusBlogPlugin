<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Configuration\BlogChannelConfigurationProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class BlogConfigurationContext implements Context
{
    public function __construct(
        private readonly BlogChannelConfigurationProviderInterface $configurationProvider,
        private readonly ObjectManager $manager,
    ) {
    }

    #[Given('the blog of the :channel channel lists :postsPerPage articles per page')]
    public function theBlogOfTheChannelListsArticlesPerPage(ChannelInterface $channel, int $postsPerPage): void
    {
        $configuration = $this->configurationProvider->getConfiguration($channel);
        $configuration->setPostsPerPage($postsPerPage);

        $this->manager->persist($configuration);
        $this->manager->flush();
    }
}
