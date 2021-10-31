<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BlogArticleCategoryFixture extends AbstractResourceFixture
{
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $node = $resourceNode->children();

        $node->scalarNode('code')->cannotBeEmpty();
        $node->booleanNode('enabled');
        $node->scalarNode('title')->cannotBeEmpty();
        $node->scalarNode('slug')->cannotBeEmpty();
    }

    public function getName(): string
    {
        return 'blog_article_category';
    }
}
