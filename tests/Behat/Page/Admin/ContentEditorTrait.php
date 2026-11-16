<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Element\NodeElement;
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

    public function getContentEditorStats(string $localeCode): string
    {
        return $this->contentEditor($localeCode)->find('css', '[data-odiseo-blog-editor-target="stats"]')?->getText() ?? '';
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
