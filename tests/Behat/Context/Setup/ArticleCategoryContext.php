<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Odiseo\BlogBundle\Doctrine\ORM\ArticleCategoryRepositoryInterface;
use Odiseo\BlogBundle\Model\ArticleCategoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ArticleCategoryContext implements Context
{
    /** @var FactoryInterface */
    private $articleCategoryFactory;

    /** @var ArticleCategoryRepositoryInterface */
    private $articleCategoryRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        FactoryInterface $articleCategoryFactory,
        ArticleCategoryRepositoryInterface $articleCategoryRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->articleCategoryFactory = $articleCategoryFactory;
        $this->articleCategoryRepository = $articleCategoryRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $code
     * @Given there is an existing article category with :code code
     */
    public function thereIsAnArticleCategoryWithCode(string $code): void
    {
        $articleCategory = $this->createArticleCategory($code, 'Sylius');

        $this->saveArticleCategory($articleCategory);
    }

    /**
     * @param array $articleCategoryCodes
     * @Given the store has( also) :firstArticleCategoryCode and :secondArticleCategoryCode article categories
     */
    public function theStoreHasArticles(...$articleCategoryCodes)
    {
        foreach ($articleCategoryCodes as $key => $articleCategoryCode) {
            $this->saveArticleCategory($this->createArticleCategory($articleCategoryCode, 'Article category '.$key));
        }
    }

    /**
     * @param string $code
     * @param string $title
     *
     * @return ArticleCategoryInterface
     */
    private function createArticleCategory(string $code, string $title): ArticleCategoryInterface
    {
        /** @var ArticleCategoryInterface $articleCategory */
        $articleCategory = $this->articleCategoryFactory->createNew();

        $articleCategory->setCode($code);
        $articleCategory->getTranslation()->setSlug($code);
        $articleCategory->getTranslation()->setTitle($title);

        return $articleCategory;
    }

    /**
     * @param ArticleCategoryInterface $articleCategory
     */
    private function saveArticleCategory(ArticleCategoryInterface $articleCategory): void
    {
        $this->articleCategoryRepository->add($articleCategory);
    }
}
