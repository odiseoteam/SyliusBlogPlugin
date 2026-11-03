<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of ArticleInterface
 * @implements ArticleRepositoryInterface<T>
 */
class ArticleRepository extends EntityRepository implements ArticleRepositoryInterface
{
}
