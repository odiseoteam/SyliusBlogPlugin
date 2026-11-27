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
                ->arrayNode('content')
                    ->info('Limits and reading speed of the content of the articles.')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('max_size')->info('Largest content that can be saved, in bytes.')->defaultValue(1048576)->min(1024)->end()
                        ->integerNode('max_depth')->info('Most nested levels a content can have.')->defaultValue(20)->min(2)->max(100)->end()
                        ->arrayNode('iframe_hosts')
                            ->info('Hosts (and their subdomains) the HTML node can load iframes from.')
                            ->scalarPrototype()->cannotBeEmpty()->end()
                            ->defaultValue(['www.youtube-nocookie.com', 'player.vimeo.com'])
                        ->end()
                        ->integerNode('words_per_minute')->info('Reading speed used for the reading time.')->defaultValue(200)->min(1)->end()
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
