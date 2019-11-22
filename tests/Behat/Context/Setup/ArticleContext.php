<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ArticleContext implements Context
{
    /** @var FactoryInterface */
    private $articleFactory;

    /** @var ArticleRepositoryInterface */
    private $articleRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        FactoryInterface $articleFactory,
        ArticleRepositoryInterface $articleRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->articleFactory = $articleFactory;
        $this->articleRepository = $articleRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $code
     * @Given there is an existing article with :code code
     */
    public function thereIsAnArticleWithCode(string $code): void
    {
        $article = $this->createArticle($code, 'Article 1', '<h1>An awesome article</h1>');

        $this->saveArticle($article);
    }

    /**
     * @param array $articleCodes
     * @Given the store has( also) :firstArticleCode and :secondArticleCode articles
     */
    public function theStoreHasArticles(...$articleCodes): void
    {
        foreach ($articleCodes as $key => $articleCode) {
            $this->saveArticle($this->createArticle($articleCode, 'Article '.$key, '<h1>An awesome article '.$key.'</h1>'));
        }
    }

    /**
     * @param string $code
     * @param string $title
     * @param string $content
     * @return ArticleInterface
     */
    private function createArticle(string $code, string $title, string $content): ArticleInterface
    {
        /** @var ArticleInterface $article */
        $article = $this->articleFactory->createNew();

        $article->setCode($code);
        $article->getTranslation()->setSlug($code);
        $article->getTranslation()->setTitle($title);
        $article->getTranslation()->setContent($content);

        return $article;
    }

    /**
     * @param ArticleInterface $article
     */
    private function saveArticle(ArticleInterface $article): void
    {
        $this->articleRepository->add($article);
    }
}
