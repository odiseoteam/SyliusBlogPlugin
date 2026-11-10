<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Service\DriverHelper;
use Webmozart\Assert\Assert;

/** The author form: code, name and slug are not translated. */
trait AuthorFormTrait
{
    public function specifyCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function isCodeDisabled(): bool
    {
        return $this->getElement('code')->hasAttribute('disabled');
    }

    public function nameIt(string $name): void
    {
        $this->getElement('name')->setValue($name);
    }

    public function setSlug(string $slug): void
    {
        $this->getElement('slug')->setValue($slug);
    }

    public function generateSlug(): void
    {
        $this->getElement('generate_slug')->click();
        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    public function attachAvatar(string $path): void
    {
        $filesPath = $this->getParameter('files_path');
        Assert::string($filesPath);

        $this->getElement('image_file')->attachFile($filesPath . $path);
    }

    public function addSocialLink(string $network, string $url): void
    {
        $this->getElement('add_social_link')->click();
        DriverHelper::waitForLiveComponentUpdate($this->getSession());

        $rows = $this->getDocument()->findAll('css', '[data-test-social-link]');
        $row = end($rows);
        Assert::isInstanceOf($row, NodeElement::class);

        $row->find('css', '[data-test-social-link-network]')?->selectOption($network);
        $row->find('css', '[data-test-social-link-url]')?->setValue($url);
    }

    public function removeSocialLink(int $position): void
    {
        $rows = $this->getDocument()->findAll('css', '[data-test-social-link]');
        Assert::keyExists($rows, $position - 1);

        $rows[$position - 1]->find('css', '[data-test-remove-social-link]')?->click();
        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    public function countSocialLinks(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-social-link]'));
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = array_merge(parent::getDefinedElements(), [
            'add_social_link' => '[data-test-add-social-link]',
            'code' => '[data-test-code]',
            'generate_slug' => '[data-test-generate-slug-button]',
            'image_file' => '[data-test-image-file]',
            'name' => '[data-test-name]',
            'slug' => '[data-test-slug]',
        ]);

        return $elements;
    }
}
