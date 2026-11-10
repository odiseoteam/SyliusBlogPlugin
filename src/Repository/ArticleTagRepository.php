<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of ArticleTagInterface
 * @implements ArticleTagRepositoryInterface<T>
 */
class ArticleTagRepository extends EntityRepository implements ArticleTagRepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;
    }
}
