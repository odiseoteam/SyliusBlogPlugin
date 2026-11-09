<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Behat\Transformation\Transform;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Odiseo\SyliusBlogPlugin\Repository\AuthorRepositoryInterface;
use Webmozart\Assert\Assert;

final class AuthorContext implements Context
{
    /** @param AuthorRepositoryInterface<AuthorInterface> $authorRepository */
    public function __construct(private readonly AuthorRepositoryInterface $authorRepository)
    {
    }

    #[Transform(':author')]
    #[Transform('/^"([^"]+)" author$/')]
    public function getAuthorByName(string $name): AuthorInterface
    {
        $author = $this->findByName($name);
        Assert::notNull($author, sprintf('Author "%s" does not exist.', $name));

        return $author;
    }

    public function findByName(string $name): ?AuthorInterface
    {
        $author = $this->authorRepository->findOneBy(['name' => $name]);

        return $author instanceof AuthorInterface ? $author : null;
    }
}
