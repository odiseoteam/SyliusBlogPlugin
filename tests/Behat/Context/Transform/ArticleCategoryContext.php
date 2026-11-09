<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Behat\Transformation\Transform;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleCategoryRepositoryInterface;
use Webmozart\Assert\Assert;

final class ArticleCategoryContext implements Context
{
    /** @param ArticleCategoryRepositoryInterface<ArticleCategoryInterface> $categoryRepository */
    public function __construct(private readonly ArticleCategoryRepositoryInterface $categoryRepository)
    {
    }

    #[Transform(':category')]
    #[Transform('/^"([^"]+)" category$/')]
    public function getCategoryByName(string $name): ArticleCategoryInterface
    {
        $category = $this->findByName($name);
        Assert::notNull($category, sprintf('Category "%s" does not exist.', $name));

        return $category;
    }

    public function findByName(string $name): ?ArticleCategoryInterface
    {
        foreach ($this->categoryRepository->findAllForTree('en_US', 'en_US') as $category) {
            if ($category['name'] === $name) {
                return $this->categoryRepository->find($category['id']);
            }
        }

        return null;
    }
}
