<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Configuration;

use Odiseo\SyliusBlogPlugin\Configuration\BlogChannelConfigurationFactory;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfiguration;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Resource\Factory\FactoryInterface;

#[CoversClass(BlogChannelConfigurationFactory::class)]
final class BlogChannelConfigurationFactoryTest extends TestCase
{
    public function testNewConfigurationsStartWithTheDefaults(): void
    {
        $configuration = $this->factory(false, 12)->createNew();

        self::assertFalse($configuration->isEnabled());
        self::assertSame(12, $configuration->getPostsPerPage());
        self::assertNull($configuration->getChannel());
    }

    public function testItCreatesTheConfigurationOfAChannel(): void
    {
        $channel = new Channel();

        $configuration = $this->factory(true, 9)->createForChannel($channel);

        self::assertSame($channel, $configuration->getChannel());
        self::assertTrue($configuration->isEnabled());
        self::assertSame(9, $configuration->getPostsPerPage());
    }

    private function factory(bool $enabled, int $postsPerPage): BlogChannelConfigurationFactory
    {
        /** @var FactoryInterface<BlogChannelConfigurationInterface>&Stub $decoratedFactory */
        $decoratedFactory = $this->createStub(FactoryInterface::class);
        $decoratedFactory->method('createNew')->willReturnCallback(static fn () => new BlogChannelConfiguration());

        return new BlogChannelConfigurationFactory($decoratedFactory, $enabled, $postsPerPage);
    }
}
