<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Behat\Transformation\Transform;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use Webmozart\Assert\Assert;

final class ArticleContext implements Context
{
    /** @param ArticleRepositoryInterface<ArticleInterface> $articleRepository */
    public function __construct(private readonly ArticleRepositoryInterface $articleRepository)
    {
    }

    #[Transform(':article')]
    #[Transform('/^"([^"]+)" article$/')]
    public function getArticleByTitle(string $title): ArticleInterface
    {
        $article = $this->articleRepository->createListQueryBuilder('en_US')
            ->andWhere('translation.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        Assert::isInstanceOf($article, ArticleInterface::class, sprintf('Article "%s" does not exist.', $title));

        return $article;
    }
}
