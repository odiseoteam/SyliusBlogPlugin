<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of AuthorInterface
 * @implements AuthorRepositoryInterface<T>
 */
class AuthorRepository extends EntityRepository implements AuthorRepositoryInterface
{
}
