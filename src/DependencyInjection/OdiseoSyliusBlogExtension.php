<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\DependencyInjection;

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
        /** @var array{channel_defaults: array{enabled: bool, posts_per_page: int}, social_networks: list<string>} $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('odiseo_blog.channel_defaults.enabled', $config['channel_defaults']['enabled']);
        $container->setParameter('odiseo_blog.channel_defaults.posts_per_page', $config['channel_defaults']['posts_per_page']);
        $container->setParameter('odiseo_blog.social_networks', $config['social_networks']);

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
