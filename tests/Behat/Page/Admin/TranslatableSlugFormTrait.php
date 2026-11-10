<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin;

use Sylius\Behat\Service\DriverHelper;

/** Code, and name and slug per locale, of the forms built on the shared admin templates. */
trait TranslatableSlugFormTrait
{
    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function isCodeDisabled(): bool
    {
        return $this->getElement('code')->hasAttribute('disabled');
    }

    public function nameIt(string $name, string $localeCode): void
    {
        $this->getElement('name', ['%locale%' => $localeCode])->setValue($name);
    }

    public function setSlug(string $slug, string $localeCode): void
    {
        $this->getElement('slug', ['%locale%' => $localeCode])->setValue($slug);
    }

    public function generateSlug(string $localeCode): void
    {
        $this->getElement('generate_slug', ['%locale%' => $localeCode])->click();
        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'generate_slug' => '[data-test-generate-slug-button="%locale%"]',
            'name' => '[data-test-name="%locale%"]',
            'slug' => '[data-test-slug="%locale%"]',
        ]);

        return $elements;
    }
}
