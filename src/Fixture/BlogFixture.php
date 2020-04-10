<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BlogFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->arrayNode('channels')->scalarPrototype()->end()->end()
                ->scalarNode('image')->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'blog';
    }
}
