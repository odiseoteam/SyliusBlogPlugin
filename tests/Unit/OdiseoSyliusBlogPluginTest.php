<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit;

use Odiseo\SyliusBlogPlugin\OdiseoSyliusBlogPlugin;
use PHPUnit\Framework\TestCase;

final class OdiseoSyliusBlogPluginTest extends TestCase
{
    public function testItPointsToThePluginRoot(): void
    {
        $plugin = new OdiseoSyliusBlogPlugin();

        self::assertFileExists($plugin->getPath() . '/composer.json');
        self::assertDirectoryExists($plugin->getPath() . '/config');
    }
}
