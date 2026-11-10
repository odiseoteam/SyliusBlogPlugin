<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ArticleTagInterface
 * @extends RepositoryInterface<T>
 */
interface ArticleTagRepositoryInterface extends RepositoryInterface
{
    /** For the admin grid, joined with the translation of the given locale. */
    public function createListQueryBuilder(string $localeCode): QueryBuilder;
}
