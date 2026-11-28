<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Shop\Article;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function hasProduct(string $name): bool;

    public function hasImage(string $alt): bool;

    public function countGalleryImages(): int;
}
