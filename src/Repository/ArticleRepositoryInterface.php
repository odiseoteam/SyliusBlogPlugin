<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ArticleInterface
 * @extends RepositoryInterface<T>
 */
interface ArticleRepositoryInterface extends RepositoryInterface
{
}
