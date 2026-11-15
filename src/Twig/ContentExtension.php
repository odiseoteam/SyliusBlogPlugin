<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ContentExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('odiseo_blog_reading_time', [ContentRuntime::class, 'readingTime']),
        ];
    }
}
