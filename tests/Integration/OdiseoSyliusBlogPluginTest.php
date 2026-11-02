<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration;

use Odiseo\SyliusBlogPlugin\OdiseoSyliusBlogPlugin;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OdiseoSyliusBlogPluginTest extends KernelTestCase
{
    public function testItIsRegisteredInTheTestApplication(): void
    {
        $bundles = self::bootKernel()->getBundles();

        self::assertArrayHasKey('OdiseoSyliusBlogPlugin', $bundles);
        self::assertInstanceOf(OdiseoSyliusBlogPlugin::class, $bundles['OdiseoSyliusBlogPlugin']);
    }
}
