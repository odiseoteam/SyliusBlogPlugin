<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ArticleContext implements Context
{
    /** @var RepositoryInterface */
    private $articleRepository;

    public function __construct(
        RepositoryInterface $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param $code
     * @Transform /^article "([^"]+)"$/
     * @Transform /^"([^"]+)" article$/
     * @return ArticleInterface
     */
    public function getArticleByCode($code): ArticleInterface
    {
        /** @var ArticleInterface $article */
        $article = $this->articleRepository->findOneBy(['code' => $code]);

        Assert::notNull(
            $article,
            'Article with code %s does not exist'
        );

        return $article;
    }
}
