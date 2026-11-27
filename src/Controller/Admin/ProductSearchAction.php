<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Products for the editor: those whose code or name (in any language) contains `query`,
 * or those with the codes in `code[]`. Each one with its code, name and thumbnail.
 */
final class ProductSearchAction
{
    private const LIMIT = 20;

    /** @param class-string<ProductInterface> $productClass */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly CacheManager $imagineCacheManager,
        private readonly string $productClass,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $builder = $this->manager->createQueryBuilder()
            ->select('product')
            ->distinct()
            ->from($this->productClass, 'product')
            ->leftJoin('product.translations', 'translation')
            ->orderBy('product.code')
            ->setMaxResults(self::LIMIT)
        ;

        /** @var list<string> $codes */
        $codes = array_values(array_filter($request->query->all('code'), 'is_string'));
        $query = trim($request->query->getString('query'));
        if ([] !== $codes) {
            $builder->andWhere('product.code IN (:codes)')->setParameter('codes', $codes);
        } elseif ('' !== $query) {
            $builder
                // Lowercase both sides: LIKE is case sensitive in PostgreSQL.
                ->andWhere('LOWER(product.code) LIKE :query OR LOWER(translation.name) LIKE :query')
                ->setParameter('query', '%' . addcslashes(mb_strtolower($query), '%_\\') . '%')
            ;
        } else {
            return new JsonResponse([]);
        }

        /** @var list<ProductInterface> $products */
        $products = $builder->getQuery()->getResult();

        return new JsonResponse(array_map($this->describe(...), $products));
    }

    /** @return array{code: string|null, name: string, image: string|null} */
    private function describe(ProductInterface $product): array
    {
        $image = $product->getImages()->first() ?: null;
        $path = $image?->getPath();

        return [
            'code' => $product->getCode(),
            'name' => $product->getName() ?? (string) $product->getCode(),
            'image' => null !== $path ? $this->imagineCacheManager->getBrowserPath($path, 'sylius_admin_product_thumbnail') : null,
        ];
    }
}
