<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Legacy;

use Odiseo\SyliusBlogPlugin\Legacy\Doctrine\LegacyTablesSchemaFilter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LegacyTablesSchemaFilter::class)]
final class LegacyTablesSchemaFilterTest extends TestCase
{
    public function testItHidesOnlyTheLegacyTables(): void
    {
        $filter = new LegacyTablesSchemaFilter();

        self::assertFalse($filter('odiseo_blog_legacy_article'));
        self::assertFalse($filter('odiseo_blog_legacy_article_id_seq'));
        self::assertTrue($filter('odiseo_blog_article'));
        self::assertTrue($filter('sylius_product'));
    }
}
