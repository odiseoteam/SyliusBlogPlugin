<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Legacy\Doctrine;

use Doctrine\DBAL\Schema\AbstractAsset;

/**
 * Keeps the renamed 1.x tables out of schema diffs, so a generated migration never drops them before they are
 * imported.
 */
final class LegacyTablesSchemaFilter
{
    public const PREFIX = 'odiseo_blog_legacy_';

    /** DBAL passes the asset name, or on 3.x the asset itself for sequences. */
    public function __invoke(mixed $asset): bool
    {
        $name = $asset instanceof AbstractAsset ? $asset->getName() : $asset;

        return !\is_string($name) || !str_starts_with($name, self::PREFIX);
    }
}
