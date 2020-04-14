<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BlogArticleFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
            ->scalarNode('code')->cannotBeEmpty()->end()
            ->booleanNode('enabled')->end()
            ->arrayNode('channels')->scalarPrototype()->end()->end()
            ->arrayNode('categories')->scalarPrototype()->end()->end()
            ->scalarNode('title')->cannotBeEmpty()->end()
            ->scalarNode('content')->cannotBeEmpty()->end()
            ->scalarNode('slug')->cannotBeEmpty()->end()
            ->scalarNode('image')->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'blog_article';
    }
}
