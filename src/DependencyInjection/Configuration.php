<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('odiseo_sylius_blog_plugin');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('disqus')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('shortname')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
