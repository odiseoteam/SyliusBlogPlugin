<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ArticleInterface
 * @extends RepositoryInterface<T>
 */
interface ArticleRepositoryInterface extends RepositoryInterface
{
    /** For the admin grid, joined with the translation of the given locale. */
    public function createListQueryBuilder(string $localeCode): QueryBuilder;
}
