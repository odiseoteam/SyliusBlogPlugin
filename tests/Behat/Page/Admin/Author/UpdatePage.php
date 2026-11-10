<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use AuthorFormTrait {
        getDefinedElements as getFormElements;
    }

    public function removeAvatar(): void
    {
        $this->getElement('remove_image')->press();
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        return array_merge($this->getFormElements(), [
            'remove_image' => '[data-test-remove-image]',
        ]);
    }
}
