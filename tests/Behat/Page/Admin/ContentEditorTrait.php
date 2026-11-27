<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Element\NodeElement;
use Facebook\WebDriver\WebDriverKeys;
use Webmozart\Assert\Assert;

/** The rich text editor of a content field; it needs JavaScript. */
trait ContentEditorTrait
{
    /** Types where the cursor is; a click would move it. */
    public function writeInContentEditor(string $text, string $localeCode): void
    {
        $surface = $this->contentEditorSurface($localeCode);
        $focused = $this->pantherDriver()->evaluateScript(sprintf(
            'document.evaluate(%s, document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue.contains(document.activeElement)',
            json_encode($surface->getXpath()),
        ));
        if (true !== $focused) {
            $surface->click();
        }

        $this->pantherDriver()->getClient()->getKeyboard()->sendKeys($text);
    }

    /** Clicks a button of the toolbar, or an option of one of its menus, by its label. */
    public function useContentEditorTool(string $label, string $localeCode): void
    {
        $editor = $this->contentEditor($localeCode);
        $button = $editor->find('css', sprintf('[data-odiseo-blog-editor-target="toolbar"] [aria-label="%s"]', $label));
        if (null === $button) {
            $option = $editor->find('xpath', sprintf('.//*[contains(@class, "dropdown-item")][normalize-space(.)="%s"]', $label));
            Assert::notNull($option, sprintf('The editor has no tool "%s".', $label));
            $option->find('xpath', 'ancestor::div[contains(concat(" ", normalize-space(@class), " "), " dropdown ")][1]//*[@data-bs-toggle="dropdown"]')?->click();
            $this->getDocument()->waitFor(5, static fn (): bool => $option->isVisible());
            $button = $option;
        }

        $button->click();
    }

    public function addVideoToContent(string $url, string $localeCode): void
    {
        $this->contentEditorSurface($localeCode);
        $this->useContentEditorTool('Video', $localeCode);
        $modal = $this->contentEditor($localeCode)->find('css', '.modal');
        Assert::notNull($modal);
        // The field gets the focus once the dialog has opened; Bootstrap ignores closing it before.
        Assert::true($this->pantherDriver()->wait(5000, 'document.activeElement?.matches(\'[data-odiseo-blog-editor-target="videoSrc"]\') === true'), 'The video dialog did not open.');

        $modal->find('css', '[data-odiseo-blog-editor-target="videoSrc"]')?->setValue($url);
        $modal->find('css', '[data-action="odiseo-blog-editor#apply"]')?->click();
        Assert::true($this->getDocument()->waitFor(5, static fn (): bool => !$modal->isVisible()), 'The video dialog did not close.');
    }

    /** Searches the product by its name in the product dialog, picks it and applies the dialog. */
    public function addProductToContent(string $name, string $localeCode): void
    {
        $this->contentEditorSurface($localeCode);
        $this->useContentEditorTool('Product', $localeCode);
        $modal = $this->contentEditor($localeCode)->find('css', '.modal');
        Assert::notNull($modal);
        // The search gets the focus once the dialog has opened; Bootstrap ignores closing it before.
        Assert::true($this->pantherDriver()->wait(5000, 'document.activeElement?.closest(\'.ts-wrapper\') !== null'), 'The product dialog did not open.');

        $this->pantherDriver()->getClient()->getKeyboard()->sendKeys($name);
        $option = sprintf('[data-odiseo-blog-editor-target="pane"][data-dialog="product"] .ts-dropdown .option');
        Assert::true($this->pantherDriver()->wait(10000, sprintf('[...document.querySelectorAll(%s)].some((option) => option.textContent.includes(%s))', json_encode($option), json_encode($name))), sprintf('No product "%s" was found.', $name));
        $this->pantherDriver()->getClient()->getKeyboard()->sendKeys(WebDriverKeys::ENTER);

        $modal->find('css', '[data-action="odiseo-blog-editor#apply"]')?->click();
        Assert::true($this->getDocument()->waitFor(5, static fn (): bool => !$modal->isVisible()), 'The product dialog did not close.');
    }

    /** Uploads the file in the image dialog, describes it and applies the dialog. */
    public function addImageToContent(string $file, string $alt, string $localeCode): void
    {
        $modal = $this->openContentDialog('Image', 'imageFile', $localeCode);
        $modal->find('css', '[data-odiseo-blog-editor-target="imageFile"]')?->attachFile($this->fixturePath($file));
        Assert::true($this->pantherDriver()->wait(10000, 'document.querySelector(\'[data-odiseo-blog-editor-target="imagePreview"]:not([hidden])\') !== null'), 'The image was not uploaded.');

        $modal->find('css', '[data-odiseo-blog-editor-target="imageAlt"]')?->setValue($alt);
        $this->applyContentDialog($modal);
    }

    /** @param list<string> $files */
    public function addGalleryToContent(array $files, string $localeCode): void
    {
        $modal = $this->openContentDialog('Gallery', 'galleryFiles', $localeCode);
        foreach ($files as $index => $file) {
            $modal->find('css', '[data-odiseo-blog-editor-target="galleryFiles"]')?->attachFile($this->fixturePath($file));
            Assert::true($this->pantherDriver()->wait(10000, sprintf('document.querySelectorAll(\'[data-odiseo-blog-editor-target="galleryList"] li\').length === %d', $index + 1)), sprintf('The image "%s" was not uploaded.', $file));
        }

        $this->applyContentDialog($modal);
    }

    public function getContentEditorStats(string $localeCode): string
    {
        return $this->contentEditor($localeCode)->find('css', '[data-odiseo-blog-editor-target="stats"]')?->getText() ?? '';
    }

    /** Opens a dialog of the toolbar and waits until its first field has the focus. */
    private function openContentDialog(string $tool, string $firstField, string $localeCode): NodeElement
    {
        $this->contentEditorSurface($localeCode);
        $this->useContentEditorTool($tool, $localeCode);
        $modal = $this->contentEditor($localeCode)->find('css', '.modal');
        Assert::notNull($modal);
        // Bootstrap ignores closing the dialog before it has opened.
        Assert::true($this->pantherDriver()->wait(5000, sprintf('document.activeElement?.matches(\'[data-odiseo-blog-editor-target="%s"]\') === true', $firstField)), sprintf('The dialog "%s" did not open.', $tool));

        return $modal;
    }

    private function applyContentDialog(NodeElement $modal): void
    {
        $modal->find('css', '[data-action="odiseo-blog-editor#apply"]')?->click();
        Assert::true($this->getDocument()->waitFor(5, static fn (): bool => !$modal->isVisible()), 'The dialog did not close.');
    }

    private function fixturePath(string $file): string
    {
        $filesPath = $this->getParameter('files_path');
        Assert::string($filesPath);

        return $filesPath . $file;
    }

    private function contentEditor(string $localeCode): NodeElement
    {
        $editor = $this->getElement('content', ['%locale%' => $localeCode])->find('xpath', 'ancestor::div[contains(@class, "odiseo-blog-editor")][1]');
        Assert::notNull($editor, 'The content has no editor.');

        return $editor;
    }

    private function contentEditorSurface(string $localeCode): NodeElement
    {
        $editor = $this->contentEditor($localeCode);
        Assert::true($this->getDocument()->waitFor(10, static fn (): bool => null !== $editor->find('css', '.ProseMirror')), 'The editor did not start.');

        /** @var NodeElement $surface */
        $surface = $editor->find('css', '.ProseMirror');

        return $surface;
    }

    private function pantherDriver(): PantherDriver
    {
        $driver = $this->getDriver();
        Assert::isInstanceOf($driver, PantherDriver::class, 'The editor needs JavaScript.');

        return $driver;
    }
}
