<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Factory;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

/** @extends FactoryInterface<ArticleCategoryInterface> */
interface ArticleCategoryFactoryInterface extends FactoryInterface
{
    public function createForParent(ArticleCategoryInterface $parent): ArticleCategoryInterface;
}
