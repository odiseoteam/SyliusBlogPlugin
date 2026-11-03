<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Odiseo\SyliusBlogPlugin\Configuration\BlogChannelConfigurationProviderInterface;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class ChannelConfigurationRuntime implements RuntimeExtensionInterface
{
    public function __construct(private BlogChannelConfigurationProviderInterface $provider)
    {
    }

    public function configuration(ChannelInterface $channel): BlogChannelConfigurationInterface
    {
        return $this->provider->getConfiguration($channel);
    }
}
