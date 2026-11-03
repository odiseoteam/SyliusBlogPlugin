<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ChannelConfigurationExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('odiseo_blog_channel_configuration', [ChannelConfigurationRuntime::class, 'configuration']),
        ];
    }
}
