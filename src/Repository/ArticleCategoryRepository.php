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
    public function findAllForTree(string $localeCode, string $fallbackLocaleCode): array
    {
        /** @var list<array{id: int|string, parentId: int|string|null, name: string}> $rows */
        $rows = $this->createQueryBuilder('o')
            ->select('o.id AS id', 'IDENTITY(o.parent) AS parentId', 'COALESCE(translation.name, fallbackTranslation.name, o.code) AS name')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->leftJoin('o.translations', 'fallbackTranslation', 'WITH', 'fallbackTranslation.locale = :fallbackLocaleCode')
            ->orderBy('o.level', 'DESC')
            ->addOrderBy('o.position', 'ASC')
            ->setParameter('localeCode', $localeCode)
            ->setParameter('fallbackLocaleCode', $fallbackLocaleCode)
            ->getQuery()
            ->getArrayResult()
        ;

        return array_map(static fn (array $row): array => [
            'id' => (int) $row['id'],
            'parentId' => null === $row['parentId'] ? null : (int) $row['parentId'],
            'name' => $row['name'],
        ], $rows);
    }
}
