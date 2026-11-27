<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Media;

/** The address of a stored image through a filter of LiipImagine. */
interface ImageUrlResolverInterface
{
    public function resolve(string $path, string $filter): string;
}
