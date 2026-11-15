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
            ['enabled' => true, 'posts_per_page' => 9],
            $this->process([])['channel_defaults'],
        );
    }

    public function testTheDefaultsCanBeChanged(): void
    {
        self::assertSame(
            ['enabled' => false, 'posts_per_page' => 12],
            $this->process([['channel_defaults' => ['enabled' => false, 'posts_per_page' => 12]]])['channel_defaults'],
        );
    }

    public function testAuthorsCanLinkToEightNetworksByDefault(): void
    {
        self::assertSame(
            ['x', 'linkedin', 'facebook', 'instagram', 'youtube', 'tiktok', 'github', 'website'],
            $this->process([])['social_networks'],
        );
    }

    public function testTheNetworksCanBeReplaced(): void
    {
        self::assertSame(
            ['mastodon'],
            $this->process([['social_networks' => ['mastodon']]])['social_networks'],
        );
    }

    public function testTheContentHasLimitsTrustsYouTubeAndVimeoAndIsReadAt200WordsPerMinute(): void
    {
        self::assertSame(
            ['max_size' => 1048576, 'max_depth' => 20, 'iframe_hosts' => ['www.youtube-nocookie.com', 'player.vimeo.com'], 'words_per_minute' => 200],
            $this->process([])['content'],
        );
    }

    public function testTheLimitsOfTheContentCanBeChanged(): void
    {
        self::assertSame(
            ['max_size' => 2048, 'max_depth' => 8, 'iframe_hosts' => ['maps.example.com'], 'words_per_minute' => 250],
            $this->process([['content' => ['max_size' => 2048, 'max_depth' => 8, 'iframe_hosts' => ['maps.example.com'], 'words_per_minute' => 250]]])['content'],
        );
    }

    public function testAContentHasAtLeastTwoLevels(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([['content' => ['max_depth' => 1]]]);
    }

    public function testAContentHasAtMostAHundredLevels(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([['content' => ['max_depth' => 101]]]);
    }

    public function testTheReadingSpeedIsAtLeastOneWordPerMinute(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([['content' => ['words_per_minute' => 0]]]);
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
