<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function removeImage(): void
    {
        $this->getElement('remove_image')->press();
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = array_merge(parent::getDefinedElements(), [
            'remove_image' => '[data-test-remove-image]',
        ]);

        return $elements;
    }
}
