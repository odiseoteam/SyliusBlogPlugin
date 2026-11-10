<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameIt(string $name, string $localeCode): void;

    public function setSlug(string $slug, string $localeCode): void;

    public function attachImage(string $path): void;

    public function getCodeValidationMessage(): string;

    /** @return list<string> */
    public function getCategoriesInTree(): array;

    public function moveDown(string $name): void;

    public function deleteCategory(string $name): void;
}
