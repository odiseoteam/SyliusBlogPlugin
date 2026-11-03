<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleTag;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameIt(string $name, string $localeCode): void;

    public function setSlug(string $slug, string $localeCode): void;

    public function generateSlug(string $localeCode): void;

    public function getCodeValidationMessage(): string;

    public function getSlugValidationMessage(string $localeCode): string;
}
