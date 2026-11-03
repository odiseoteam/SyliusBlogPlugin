<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTag;
use Odiseo\SyliusBlogPlugin\Entity\Author;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/** Runs each test in a transaction that is rolled back. */
abstract class DatabaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $this->entityManager = $entityManager;
        $this->entityManager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    protected function article(string $code, string $slug = 'post', string $locale = 'en_US'): Article
    {
        $article = new Article();
        $article->setCurrentLocale($locale);
        $article->setFallbackLocale($locale);
        $article->setCode($code);
        $article->setTitle(ucfirst($slug));
        $article->setSlug($slug);

        return $article;
    }

    protected function category(string $code, ?ArticleCategory $parent = null): ArticleCategory
    {
        $category = new ArticleCategory();
        $category->setCurrentLocale('en_US');
        $category->setFallbackLocale('en_US');
        $category->setCode($code);
        $category->setName(ucfirst($code));
        $category->setSlug($code);
        $category->setParent($parent);

        return $category;
    }

    protected function tag(string $code): ArticleTag
    {
        $tag = new ArticleTag();
        $tag->setCurrentLocale('en_US');
        $tag->setFallbackLocale('en_US');
        $tag->setCode($code);
        $tag->setName(ucfirst($code));
        $tag->setSlug($code);

        return $tag;
    }

    protected function author(string $code): Author
    {
        $author = new Author();
        $author->setCurrentLocale('en_US');
        $author->setFallbackLocale('en_US');
        $author->setCode($code);
        $author->setName(ucfirst($code));
        $author->setSlug($code);

        return $author;
    }

    protected function channel(string $code): Channel
    {
        $locale = $this->entityManager->getRepository(Locale::class)->findOneBy(['code' => 'en_US']) ?? new Locale();
        $locale->setCode('en_US');
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']) ?? new Currency();
        $currency->setCode('USD');

        $channel = new Channel();
        $channel->setCode($code);
        $channel->setName($code);
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setDefaultLocale($locale);
        $channel->setBaseCurrency($currency);
        $this->entityManager->persist($locale);
        $this->entityManager->persist($currency);
        $this->entityManager->persist($channel);

        return $channel;
    }

    protected function adminUser(string $username): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->setUsername($username);
        $adminUser->setEmail($username . '@example.com');
        $adminUser->setPassword('not-a-real-hash');
        $adminUser->setLocaleCode('en_US');
        $this->entityManager->persist($adminUser);

        return $adminUser;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    protected function reload(string $class, ?int $id): object
    {
        $this->entityManager->clear();

        $entity = $this->entityManager->find($class, $id);
        self::assertInstanceOf($class, $entity);

        return $entity;
    }
}
