<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Entity;

use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BlogChannelConfiguration::class)]
final class BlogChannelConfigurationTest extends TestCase
{
    public function testTheTitleAndDescriptionGoToTheCurrentLocale(): void
    {
        $configuration = new BlogChannelConfiguration();
        $configuration->setCurrentLocale('es_AR');
        $configuration->setFallbackLocale('en_US');

        $configuration->setTitle('Bitácora');
        $configuration->setDescription('Historias de la tienda.');

        self::assertSame('Bitácora', $configuration->getTranslation('es_AR')->getTitle());
        self::assertSame('Historias de la tienda.', $configuration->getTranslation('es_AR')->getDescription());
    }

    public function testItIsEnabledUntilTurnedOff(): void
    {
        $configuration = new BlogChannelConfiguration();
        self::assertTrue($configuration->isEnabled());

        $configuration->disable();
        self::assertFalse($configuration->isEnabled());
    }
}
