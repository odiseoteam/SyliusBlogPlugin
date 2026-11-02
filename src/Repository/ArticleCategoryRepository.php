<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of ArticleCategoryInterface
 * @implements ArticleCategoryRepositoryInterface<T>
 */
class ArticleCategoryRepository extends EntityRepository implements ArticleCategoryRepositoryInterface
{
}
