<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleTag;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\TranslatableSlugFormTrait;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use TranslatableSlugFormTrait;

    public function getCodeValidationMessage(): string
    {
        return $this->getValidationMessage('code');
    }

    public function getSlugValidationMessage(string $localeCode): string
    {
        return $this->getValidationMessage('slug', ['%locale%' => $localeCode]);
    }
}
