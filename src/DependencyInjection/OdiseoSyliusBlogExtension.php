<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\DependencyInjection;

use Odiseo\SyliusBlogPlugin\Content\Marks\ContentMarkInterface;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ContentNodeInterface;
use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class OdiseoSyliusBlogExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var array{channel_defaults: array{enabled: bool, posts_per_page: int}, content: array{max_size: int, max_depth: int, iframe_hosts: list<string>, words_per_minute: int}, social_networks: list<string>} $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('odiseo_blog.channel_defaults.enabled', $config['channel_defaults']['enabled']);
        $container->setParameter('odiseo_blog.channel_defaults.posts_per_page', $config['channel_defaults']['posts_per_page']);
        $container->setParameter('odiseo_blog.social_networks', $config['social_networks']);
        $container->setParameter('odiseo_blog.content.max_size', $config['content']['max_size']);
        $container->setParameter('odiseo_blog.content.max_depth', $config['content']['max_depth']);
        $container->setParameter('odiseo_blog.content.iframe_hosts', $config['content']['iframe_hosts']);
        $container->setParameter('odiseo_blog.content.words_per_minute', $config['content']['words_per_minute']);

        $container->registerForAutoconfiguration(ContentNodeInterface::class)->addTag('odiseo_blog.content_node');
        $container->registerForAutoconfiguration(ContentMarkInterface::class)->addTag('odiseo_blog.content_mark');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Odiseo\SyliusBlogPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@OdiseoSyliusBlogPlugin/src/Migrations';
    }

    /** @return string[] */
    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return ['Sylius\Bundle\CoreBundle\Migrations'];
    }
}
