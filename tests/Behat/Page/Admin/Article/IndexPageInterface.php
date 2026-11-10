<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function search(string $phrase): void;

    public function chooseStateFilter(string $state): void;

    public function chooseChannelFilter(string $channelName): void;

    public function choosePublishedBetween(string $from, string $to): void;

    public function chooseCategoryFilter(string $categoryName): void;
}
