<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ArticleCategoryInterface
 * @extends RepositoryInterface<T>
 */
interface ArticleCategoryRepositoryInterface extends RepositoryInterface
{
}
