<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Factory;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ArticleCategoryFactory implements ArticleCategoryFactoryInterface
{
    /** @param FactoryInterface<ArticleCategoryInterface> $decoratedFactory */
    public function __construct(private readonly FactoryInterface $decoratedFactory)
    {
    }

    public function createNew(): ArticleCategoryInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createForParent(ArticleCategoryInterface $parent): ArticleCategoryInterface
    {
        $category = $this->createNew();
        $category->setParent($parent);

        return $category;
    }
}
