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
        $node = $resourceNode->children();

        $node->scalarNode('code')->cannotBeEmpty();
        $node->booleanNode('enabled');
        $node->arrayNode('channels')->scalarPrototype();
        $node->arrayNode('categories')->scalarPrototype();
        $node->scalarNode('title')->cannotBeEmpty();
        $node->scalarNode('content')->cannotBeEmpty();
        $node->scalarNode('slug')->cannotBeEmpty();
        $node->scalarNode('image');
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'blog_article';
    }
}
