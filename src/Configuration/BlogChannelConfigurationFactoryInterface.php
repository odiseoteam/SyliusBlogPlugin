<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Configuration;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;

/** @extends FactoryInterface<BlogChannelConfigurationInterface> */
interface BlogChannelConfigurationFactoryInterface extends FactoryInterface
{
    public function createForChannel(ChannelInterface $channel): BlogChannelConfigurationInterface;
}
