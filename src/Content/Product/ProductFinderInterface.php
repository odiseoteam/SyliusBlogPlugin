<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Product;

use Sylius\Component\Core\Model\ProductInterface;

/** Finds the product a product node shows. */
interface ProductFinderInterface
{
    /** The enabled product of the current channel with this code, or null. */
    public function find(string $code): ?ProductInterface;

    /**
     * Loads these products at once, so finding them later needs no queries.
     *
     * @param list<string> $codes
     */
    public function preload(array $codes): void;
}
