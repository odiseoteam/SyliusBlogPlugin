<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('odiseo_blog_image_url', [ImageRuntime::class, 'url']),
            new TwigFunction('odiseo_blog_image_alt', [ImageRuntime::class, 'alt']),
        ];
    }
}
