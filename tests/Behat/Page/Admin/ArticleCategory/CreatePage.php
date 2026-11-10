<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ArticleCategory;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\DriverHelper;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\TranslatableSlugFormTrait;
use Webmozart\Assert\Assert;

/** The category form, with the tree next to it. */
final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use TranslatableSlugFormTrait {
        getDefinedElements as getFormElements;
    }

    public function attachImage(string $path): void
    {
        $filesPath = $this->getParameter('files_path');
        Assert::string($filesPath);

        $this->getElement('image_file')->attachFile($filesPath . $path);
    }

    public function getCodeValidationMessage(): string
    {
        return $this->getValidationMessage('code');
    }

    public function getCategoriesInTree(): array
    {
        // The tree is drawn by JavaScript after the page loads.
        DriverHelper::waitForElement($this->getSession(), '[data-test-tree-categories] [data-test-tree-category]');

        return array_values(array_map(
            static fn (NodeElement $category): string => (string) $category->getAttribute('data-test-tree-category'),
            $this->getElement('tree')->findAll('css', '[data-test-tree-category]'),
        ));
    }

    public function moveDown(string $name): void
    {
        $this->openActionsOf($name)->find('css', '[data-test-move-down]')?->click();
        DriverHelper::waitForLiveComponentUpdate($this->getSession());
    }

    public function deleteCategory(string $name): void
    {
        $this->openActionsOf($name)->find('css', '[data-test-delete]')?->click();

        $confirm = '.modal.show [data-test-confirm-button]';
        $this->getDocument()->waitFor(5, fn (): bool => null !== $this->getDocument()->find('css', $confirm));
        $this->getDocument()->find('css', $confirm)?->press();
        DriverHelper::waitForPageToLoad($this->getSession());
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        return array_merge($this->getFormElements(), [
            'image_file' => '[data-test-image-file]',
            'tree' => '[data-test-tree-categories]',
            'tree_category' => '[data-test-tree-categories] [data-test-tree-category="%name%"]',
        ]);
    }

    private function openActionsOf(string $name): NodeElement
    {
        $this->getCategoriesInTree();
        $category = $this->getElement('tree_category', ['%name%' => $name]);
        $category->find('css', '[data-test-actions]')?->click();

        return $category;
    }
}
