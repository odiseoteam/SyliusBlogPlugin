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
    /**
     * Every category, deepest first and by position, named in the locale or else in the fallback one.
     *
     * @return list<array{id: int, parentId: int|null, name: string}>
     */
    public function findAllForTree(string $localeCode, string $fallbackLocaleCode): array;
}
