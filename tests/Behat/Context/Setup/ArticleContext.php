<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ArticleContext implements Context
{
    /** @param FactoryInterface<ArticleInterface> $articleFactory */
    public function __construct(
        private readonly FactoryInterface $articleFactory,
        private readonly ObjectManager $manager,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly SharedStorageInterface $sharedStorage,
    ) {
    }

    #[Given('the blog has an article :title')]
    public function theBlogHasAnArticle(string $title): void
    {
        $this->save($this->article($title));
    }

    #[Given('the blog has a published article :title')]
    public function theBlogHasAPublishedArticle(string $title): void
    {
        $article = $this->article($title);
        $article->setState(ArticleInterface::STATE_PUBLISHED);
        $article->setPublishedAt(new \DateTime());

        $this->save($article);
    }

    #[Given('the blog has an article :title published on :date')]
    public function theBlogHasAnArticlePublishedOn(string $title, string $date): void
    {
        $article = $this->article($title);
        $article->setState(ArticleInterface::STATE_PUBLISHED);
        $article->setPublishedAt(new \DateTime($date . ' 10:00'));

        $this->save($article);
    }

    #[Given('the blog has an article :title in the :channel channel')]
    public function theBlogHasAnArticleInTheChannel(string $title, ChannelInterface $channel): void
    {
        $article = $this->article($title);
        foreach ($article->getChannels() as $current) {
            $article->removeChannel($current);
        }
        $article->addChannel($channel);

        $this->save($article);
    }

    #[Given('the blog has an article :title in the :category category')]
    public function theBlogHasAnArticleInTheCategory(string $title, ArticleCategoryInterface $category): void
    {
        $article = $this->article($title);
        $article->addCategory($category);

        $this->save($article);
    }

    /** A draft in the channel of the store. */
    private function article(string $title): ArticleInterface
    {
        $slug = $this->slugGenerator->generate($title);
        $article = $this->articleFactory->createNew();
        $article->setCode(strtoupper(str_replace('-', '_', $slug)));
        $article->setTitle($title);
        $article->setSlug($slug);

        if ($this->sharedStorage->has('channel')) {
            /** @var ChannelInterface $channel */
            $channel = $this->sharedStorage->get('channel');
            $article->addChannel($channel);
        }

        return $article;
    }

    private function save(ArticleInterface $article): void
    {
        $this->manager->persist($article);
        $this->manager->flush();
    }
}
