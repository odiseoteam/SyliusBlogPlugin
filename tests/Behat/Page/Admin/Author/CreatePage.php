<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use AuthorFormTrait;

    public function getCodeValidationMessage(): string
    {
        return $this->getValidationMessage('code');
    }

    public function getSlugValidationMessage(): string
    {
        return $this->getValidationMessage('slug');
    }
}
