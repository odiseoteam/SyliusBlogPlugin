<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of ArticleTagInterface
 * @extends RepositoryInterface<T>
 */
interface ArticleTagRepositoryInterface extends RepositoryInterface
{
}
