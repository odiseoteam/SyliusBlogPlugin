<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\DependencyInjection;

use Odiseo\SyliusBlogPlugin\DependencyInjection\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    public function testTheBlogIsEnabledWithNineArticlesPerPageByDefault(): void
    {
        self::assertSame(
            ['channel_defaults' => ['enabled' => true, 'posts_per_page' => 9]],
            $this->process([]),
        );
    }

    public function testTheDefaultsCanBeChanged(): void
    {
        self::assertSame(
            ['channel_defaults' => ['enabled' => false, 'posts_per_page' => 12]],
            $this->process([['channel_defaults' => ['enabled' => false, 'posts_per_page' => 12]]]),
        );
    }

    public function testAPageHasAtLeastOneArticle(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([['channel_defaults' => ['posts_per_page' => 0]]]);
    }

    /**
     * @param array<array<string, mixed>> $configs
     *
     * @return array<string, mixed>
     */
    private function process(array $configs): array
    {
        /** @var array<string, mixed> $config */
        $config = (new Processor())->processConfiguration(new Configuration(), $configs);

        return $config;
    }
}
