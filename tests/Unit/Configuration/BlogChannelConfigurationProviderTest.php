<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Configuration;

use Odiseo\SyliusBlogPlugin\Configuration\BlogChannelConfigurationFactoryInterface;
use Odiseo\SyliusBlogPlugin\Configuration\BlogChannelConfigurationProvider;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfiguration;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Odiseo\SyliusBlogPlugin\Repository\BlogChannelConfigurationRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;

#[CoversClass(BlogChannelConfigurationProvider::class)]
final class BlogChannelConfigurationProviderTest extends TestCase
{
    public function testItReturnsTheStoredConfiguration(): void
    {
        $channel = new Channel();
        $stored = new BlogChannelConfiguration();

        /** @var BlogChannelConfigurationRepositoryInterface<BlogChannelConfigurationInterface>&Stub $repository */
        $repository = $this->createStub(BlogChannelConfigurationRepositoryInterface::class);
        $repository->method('findOneByChannel')->willReturn($stored);
        $factory = $this->createMock(BlogChannelConfigurationFactoryInterface::class);
        $factory->expects(self::never())->method('createForChannel');

        self::assertSame($stored, (new BlogChannelConfigurationProvider($repository, $factory))->getConfiguration($channel));
    }

    public function testAChannelWithoutConfigurationGetsTheDefaults(): void
    {
        $channel = new Channel();
        $new = new BlogChannelConfiguration();

        /** @var BlogChannelConfigurationRepositoryInterface<BlogChannelConfigurationInterface>&Stub $repository */
        $repository = $this->createStub(BlogChannelConfigurationRepositoryInterface::class);
        $repository->method('findOneByChannel')->willReturn(null);
        $factory = $this->createMock(BlogChannelConfigurationFactoryInterface::class);
        $factory->expects(self::once())->method('createForChannel')->with($channel)->willReturn($new);

        self::assertSame($new, (new BlogChannelConfigurationProvider($repository, $factory))->getConfiguration($channel));
    }
}
