<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of ArticleTagInterface
 * @implements ArticleTagRepositoryInterface<T>
 */
class ArticleTagRepository extends EntityRepository implements ArticleTagRepositoryInterface
{
}
