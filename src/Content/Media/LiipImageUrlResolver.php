<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Media;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

final class LiipImageUrlResolver implements ImageUrlResolverInterface
{
    public function __construct(private readonly CacheManager $cacheManager)
    {
    }

    public function resolve(string $path, string $filter): string
    {
        return $this->cacheManager->getBrowserPath($path, $filter);
    }
}
