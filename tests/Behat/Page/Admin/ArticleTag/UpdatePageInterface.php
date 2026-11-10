<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleTag;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function nameIt(string $name, string $localeCode): void;

    public function isCodeDisabled(): bool;
}
