<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleRepositoryInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ArticleRepositoryTest extends DatabaseTestCase
{
    public function testTheListIsJoinedWithTheTranslationOfALocale(): void
    {
        $repository = self::getContainer()->get('odiseo_blog.repository.article');
        self::assertInstanceOf(ArticleRepositoryInterface::class, $repository);

        $this->entityManager->persist($this->article('list_tea', 'list-tea'));
        $this->entityManager->persist($this->article('list_brewing', 'list-brewing'));
        $this->entityManager->flush();

        $codes = static function (string $localeCode) use ($repository): array {
            /** @var list<ArticleInterface> $articles */
            $articles = $repository->createListQueryBuilder($localeCode)
                ->andWhere('o.code IN (:codes)')
                ->setParameter('codes', ['list_tea', 'list_brewing'])
                ->orderBy('translation.title')
                ->getQuery()
                ->getResult()
            ;

            return array_map(static fn (ArticleInterface $article): ?string => $article->getCode(), $articles);
        };

        self::assertSame(['list_brewing', 'list_tea'], $codes('en_US'));
        // Articles without a translation in the locale are still listed.
        self::assertCount(2, $codes('es_AR'));
    }
}
