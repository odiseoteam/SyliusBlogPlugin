<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Odiseo\BlogBundle\Model\ArticleCategoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ArticleCategoryContext implements Context
{
    /** @var RepositoryInterface */
    private $articleCategoryRepository;

    public function __construct(
        RepositoryInterface $articleCategoryRepository
    ) {
        $this->articleCategoryRepository = $articleCategoryRepository;
    }

    /**
     * @param string $code
     * @Transform /^article category "([^"]+)"$/
     * @Transform /^"([^"]+)" article category$/
     * @return ArticleCategoryInterface
     */
    public function getArticleCategoryByCode(string $code): ArticleCategoryInterface
    {
        /** @var ArticleCategoryInterface $articleCategory */
        $articleCategory = $this->articleCategoryRepository->findOneBy(['code' => $code]);

        Assert::notNull(
            $articleCategory,
            'Article category with code %s does not exist'
        );

        return $articleCategory;
    }
}
