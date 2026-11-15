<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\DependencyInjection;

use Odiseo\SyliusBlogPlugin\Content\Marks\ContentMarkInterface;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ContentNodeInterface;
use Odiseo\SyliusBlogPlugin\DependencyInjection\OdiseoSyliusBlogExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(OdiseoSyliusBlogExtension::class)]
final class OdiseoSyliusBlogExtensionTest extends TestCase
{
    public function testTheSettingsOfTheContentAreParameters(): void
    {
        $container = $this->load([['content' => ['max_size' => 4096, 'max_depth' => 6, 'iframe_hosts' => ['maps.example.com'], 'words_per_minute' => 250]]]);

        self::assertSame(4096, $container->getParameter('odiseo_blog.content.max_size'));
        self::assertSame(6, $container->getParameter('odiseo_blog.content.max_depth'));
        self::assertSame(['maps.example.com'], $container->getParameter('odiseo_blog.content.iframe_hosts'));
        self::assertSame(250, $container->getParameter('odiseo_blog.content.words_per_minute'));
    }

    public function testNodesAndMarksOfAnApplicationAreTaggedByImplementingTheirInterface(): void
    {
        $autoconfigured = $this->load([])->getAutoconfiguredInstanceof();

        self::assertArrayHasKey(ContentNodeInterface::class, $autoconfigured);
        self::assertArrayHasKey('odiseo_blog.content_node', $autoconfigured[ContentNodeInterface::class]->getTags());
        self::assertArrayHasKey(ContentMarkInterface::class, $autoconfigured);
        self::assertArrayHasKey('odiseo_blog.content_mark', $autoconfigured[ContentMarkInterface::class]->getTags());
    }

    /** @param array<array<string, mixed>> $configs */
    private function load(array $configs): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new OdiseoSyliusBlogExtension())->load($configs, $container);

        return $container;
    }
}
