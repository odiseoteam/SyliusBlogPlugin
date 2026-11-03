<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\EventListener;

use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfiguration;
use Odiseo\SyliusBlogPlugin\EventListener\ChannelConfigurationPersistListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\GenericEvent;

#[CoversClass(ChannelConfigurationPersistListener::class)]
final class ChannelConfigurationPersistListenerTest extends TestCase
{
    public function testItPersistsTheConfiguration(): void
    {
        $configuration = new BlogChannelConfiguration();

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('persist')->with($configuration);

        (new ChannelConfigurationPersistListener($manager))->persist(new GenericEvent($configuration));
    }
}
