<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('odiseo_sylius_blog');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('channel_defaults')
                    ->info('Settings of the channels the admin has not configured yet.')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->integerNode('posts_per_page')->defaultValue(9)->min(1)->max(100)->end()
                    ->end()
                ->end()
                ->arrayNode('social_networks')
                    ->info('Networks an author can link to. Each needs the translation odiseo_blog.social_network.<code>.')
                    ->scalarPrototype()->cannotBeEmpty()->end()
                    ->defaultValue(['x', 'linkedin', 'facebook', 'instagram', 'youtube', 'tiktok', 'github', 'website'])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
