<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Media;

/**
 * Tells which uploaded files are in use, so they are not pruned. Tag the service with
 * `odiseo_blog.media_usage_provider` (implementing this interface is enough with autoconfigure).
 */
interface MediaUsageProviderInterface
{
    /** @return iterable<string> Storage paths of the files in use. Repeats are fine. */
    public function getUsedPaths(): iterable;
}
