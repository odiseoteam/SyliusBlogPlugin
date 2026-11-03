<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Behat\Transformation\Transform;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleTagRepositoryInterface;
use Webmozart\Assert\Assert;

final class ArticleTagContext implements Context
{
    /** @param ArticleTagRepositoryInterface<ArticleTagInterface> $tagRepository */
    public function __construct(private readonly ArticleTagRepositoryInterface $tagRepository)
    {
    }

    #[Transform(':tag')]
    #[Transform('/^"([^"]+)" tag$/')]
    public function getTagByName(string $name): ArticleTagInterface
    {
        $tag = $this->tagRepository->createListQueryBuilder('en_US')
            ->andWhere('translation.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        Assert::isInstanceOf($tag, ArticleTagInterface::class, sprintf('Tag "%s" does not exist.', $name));

        return $tag;
    }
}
