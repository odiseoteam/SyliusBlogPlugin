<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Repository;

use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Odiseo\SyliusBlogPlugin\Repository\ArticleTagRepositoryInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ArticleTagRepositoryTest extends DatabaseTestCase
{
    public function testTheListIsJoinedWithTheTranslationOfALocale(): void
    {
        $repository = self::getContainer()->get('odiseo_blog.repository.article_tag');
        self::assertInstanceOf(ArticleTagRepositoryInterface::class, $repository);

        $this->entityManager->persist($this->tag('list_roasting'));
        $this->entityManager->persist($this->tag('list_brewing'));
        $this->entityManager->flush();

        $codes = static function (string $localeCode) use ($repository): array {
            /** @var list<ArticleTagInterface> $tags */
            $tags = $repository->createListQueryBuilder($localeCode)
                ->andWhere('o.code IN (:codes)')
                ->setParameter('codes', ['list_roasting', 'list_brewing'])
                ->orderBy('translation.name')
                ->getQuery()
                ->getResult()
            ;

            return array_map(static fn (ArticleTagInterface $tag): ?string => $tag->getCode(), $tags);
        };

        self::assertSame(['list_brewing', 'list_roasting'], $codes('en_US'));
        // Tags without a translation in the locale are still listed.
        self::assertCount(2, $codes('es_AR'));
    }
}
