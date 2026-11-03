<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of BlogChannelConfigurationInterface
 * @extends RepositoryInterface<T>
 */
interface BlogChannelConfigurationRepositoryInterface extends RepositoryInterface
{
    /** @return T|null */
    public function findOneByChannel(ChannelInterface $channel): ?BlogChannelConfigurationInterface;
}
