<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @template T of BlogChannelConfigurationInterface
 * @implements BlogChannelConfigurationRepositoryInterface<T>
 */
class BlogChannelConfigurationRepository extends EntityRepository implements BlogChannelConfigurationRepositoryInterface
{
    public function findOneByChannel(ChannelInterface $channel): ?BlogChannelConfigurationInterface
    {
        /** @var T|null $configuration */
        $configuration = $this->findOneBy(['channel' => $channel]);

        return $configuration;
    }
}
