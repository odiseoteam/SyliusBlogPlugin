<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Configuration;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

interface BlogChannelConfigurationProviderInterface
{
    /** The stored configuration, or an unsaved one with the defaults when the channel has none. */
    public function getConfiguration(ChannelInterface $channel): BlogChannelConfigurationInterface;
}
