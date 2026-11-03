<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Configuration;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;

/** New configurations start with the defaults from odiseo_sylius_blog.channel_defaults. */
final class BlogChannelConfigurationFactory implements BlogChannelConfigurationFactoryInterface
{
    /** @param FactoryInterface<BlogChannelConfigurationInterface> $decoratedFactory */
    public function __construct(
        private readonly FactoryInterface $decoratedFactory,
        private readonly bool $enabled,
        private readonly int $postsPerPage,
    ) {
    }

    public function createNew(): BlogChannelConfigurationInterface
    {
        $configuration = $this->decoratedFactory->createNew();
        $configuration->setEnabled($this->enabled);
        $configuration->setPostsPerPage($this->postsPerPage);

        return $configuration;
    }

    public function createForChannel(ChannelInterface $channel): BlogChannelConfigurationInterface
    {
        $configuration = $this->createNew();
        $configuration->setChannel($channel);

        return $configuration;
    }
}
