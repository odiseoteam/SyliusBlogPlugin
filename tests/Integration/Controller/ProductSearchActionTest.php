<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Controller;

use Odiseo\SyliusBlogPlugin\Controller\Admin\ProductSearchAction;
use Sylius\Component\Core\Model\Product;
use Symfony\Component\HttpFoundation\Request;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ProductSearchActionTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->product('SEARCH_MUG', ['en_US' => 'Search mug', 'es_AR' => 'Taza de búsqueda']);
        $this->product('SEARCH_100%', ['en_US' => 'Search beans']);
        $this->product('SEARCH_POT', ['en_US' => 'Search pot']);
        $this->entityManager->flush();
    }

    public function testItSearchesByCodeOrByTheNameInAnyLanguage(): void
    {
        self::assertSame(['SEARCH_MUG'], $this->codes(['query' => 'taza de búsqueda']));
        self::assertSame(['SEARCH_100%', 'SEARCH_MUG', 'SEARCH_POT'], $this->codes(['query' => 'search_']));
        self::assertSame(['SEARCH_POT'], $this->codes(['query' => 'search pot']));
    }

    public function testWildcardsAreSearchedAsText(): void
    {
        self::assertSame(['SEARCH_100%'], $this->codes(['query' => 'search_100%']));
        self::assertSame([], $this->codes(['query' => 'search%pot']));
    }

    public function testItDescribesProductsByCode(): void
    {
        $response = $this->search(['code' => ['SEARCH_POT', 'GONE']]);

        self::assertSame([['code' => 'SEARCH_POT', 'name' => 'Search pot', 'image' => null]], $response);
    }

    public function testItFindsNothingWithoutAQuery(): void
    {
        self::assertSame([], $this->search(['query' => '  ']));
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return list<string>
     */
    private function codes(array $query): array
    {
        return array_column($this->search($query), 'code');
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return list<array{code: string, name: string, image: string|null}>
     */
    private function search(array $query): array
    {
        $action = self::getContainer()->get('odiseo_blog.controller.admin.product_search');
        self::assertInstanceOf(ProductSearchAction::class, $action);

        /** @var list<array{code: string, name: string, image: string|null}> $products */
        $products = json_decode((string) $action(new Request($query))->getContent(), true, flags: \JSON_THROW_ON_ERROR);

        return $products;
    }

    /** @param array<string, string> $names */
    private function product(string $code, array $names): void
    {
        $product = new Product();
        $product->setCode($code);
        foreach ($names as $locale => $name) {
            $product->setCurrentLocale($locale);
            $product->setFallbackLocale('en_US');
            $product->setName($name);
            $product->setSlug(strtolower($code) . '-' . $locale);
        }
        $product->setCurrentLocale('en_US');
        $this->entityManager->persist($product);
    }
}
