<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of AuthorInterface
 * @extends RepositoryInterface<T>
 */
interface AuthorRepositoryInterface extends RepositoryInterface
{
}
