<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Configuration;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Odiseo\SyliusBlogPlugin\Repository\BlogChannelConfigurationRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

final class BlogChannelConfigurationProvider implements BlogChannelConfigurationProviderInterface
{
    /** @param BlogChannelConfigurationRepositoryInterface<BlogChannelConfigurationInterface> $repository */
    public function __construct(
        private readonly BlogChannelConfigurationRepositoryInterface $repository,
        private readonly BlogChannelConfigurationFactoryInterface $factory,
    ) {
    }

    public function getConfiguration(ChannelInterface $channel): BlogChannelConfigurationInterface
    {
        return $this->repository->findOneByChannel($channel) ?? $this->factory->createForChannel($channel);
    }
}
