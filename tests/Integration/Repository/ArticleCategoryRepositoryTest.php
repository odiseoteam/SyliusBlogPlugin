<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Repository;

use Odiseo\SyliusBlogPlugin\Repository\ArticleCategoryRepositoryInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ArticleCategoryRepositoryTest extends DatabaseTestCase
{
    public function testTheTreeListsEveryCategoryDeepestFirst(): void
    {
        $repository = self::getContainer()->get('odiseo_blog.repository.article_category');
        self::assertInstanceOf(ArticleCategoryRepositoryInterface::class, $repository);

        $coffee = $this->category('tree_coffee');
        $brewing = $this->category('tree_brewing', $coffee);
        $this->entityManager->persist($coffee);
        $this->entityManager->persist($brewing);
        $this->entityManager->flush();

        $rows = array_values(array_filter(
            $repository->findAllForTree('en_US', 'en_US'),
            static fn (array $row): bool => in_array($row['name'], ['Tree_coffee', 'Tree_brewing'], true),
        ));

        self::assertSame(['Tree_brewing', 'Tree_coffee'], array_column($rows, 'name'));
        self::assertSame($coffee->getId(), $rows[0]['parentId']);
        self::assertNull($rows[1]['parentId']);
    }

    public function testACategoryWithoutTranslationInTheLocaleUsesTheFallbackOrItsCode(): void
    {
        $repository = self::getContainer()->get('odiseo_blog.repository.article_category');
        self::assertInstanceOf(ArticleCategoryRepositoryInterface::class, $repository);

        $this->entityManager->persist($this->category('tree_fallback'));
        $this->entityManager->flush();

        $names = static fn (string $locale, string $fallback): array => array_column(
            array_filter(
                $repository->findAllForTree($locale, $fallback),
                static fn (array $row): bool => str_contains(strtolower($row['name']), 'tree_fallback'),
            ),
            'name',
        );

        self::assertSame(['Tree_fallback'], $names('es_AR', 'en_US'));
        // Without any translation, the code is shown.
        self::assertSame(['tree_fallback'], $names('es_AR', 'fr_FR'));
    }
}
