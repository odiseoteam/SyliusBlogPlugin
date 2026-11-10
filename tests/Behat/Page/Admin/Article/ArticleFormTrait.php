<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

use Behat\Mink\Exception\ElementNotFoundException;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\AutocompleteTrait;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\TranslatableSlugFormTrait;
use Webmozart\Assert\Assert;

/** The article form; categories and tags are autocompletes, so they need JavaScript. */
trait ArticleFormTrait
{
    use AutocompleteTrait;
    use TranslatableSlugFormTrait {
        getDefinedElements as getSlugFormElements;
    }

    public function titleIt(string $title, string $localeCode): void
    {
        $this->getElement('title', ['%locale%' => $localeCode])->setValue($title);
    }

    public function setExcerpt(string $excerpt, string $localeCode): void
    {
        $this->getElement('excerpt', ['%locale%' => $localeCode])->setValue($excerpt);
    }

    public function setContent(string $content, string $localeCode): void
    {
        $this->getElement('content', ['%locale%' => $localeCode])->setValue($content);
    }

    public function markAsFeatured(): void
    {
        $this->getElement('featured')->check();
    }

    public function isAvailableInChannel(string $channelName): bool
    {
        return $this->getElement('channels')->findField($channelName)?->isChecked() ?? false;
    }

    public function makeUnavailableInChannel(string $channelName): void
    {
        $this->getElement('channels')->uncheckField($channelName);
    }

    public function setMainCategory(string $name): void
    {
        $this->selectInAutocomplete($this->getElement('main_category'), $name);
    }

    public function addCategory(string $name): void
    {
        $this->selectInAutocomplete($this->getElement('categories'), $name);
    }

    public function addTag(string $name): void
    {
        $this->selectInAutocomplete($this->getElement('tags'), $name);
    }

    public function attachImage(string $path): void
    {
        $filesPath = $this->getParameter('files_path');
        Assert::string($filesPath);

        $this->getElement('image_file')->attachFile($filesPath . $path);
    }

    public function describeImage(string $alt, string $localeCode): void
    {
        $this->getElement('image_alt', ['%locale%' => $localeCode])->setValue($alt);
    }

    public function hasMissingAltWarning(): bool
    {
        return $this->hasElement('missing_alt');
    }

    public function getLocalesWithoutAlt(): string
    {
        return $this->getElement('missing_alt')->getText();
    }

    public function addAuthor(string $name): void
    {
        $this->selectInAutocomplete($this->getElement('authors'), $name);
    }

    public function removeAuthor(string $name): void
    {
        $this->removeFromAutocomplete($this->getElement('authors'), $name);
    }

    public function getCodeValidationMessage(): string
    {
        return $this->validationMessageOf('code');
    }

    public function getSlugValidationMessage(string $localeCode): string
    {
        return $this->validationMessageOf('slug', ['%locale%' => $localeCode]);
    }

    public function getContentValidationMessage(string $localeCode): string
    {
        return $this->validationMessageOf('content', ['%locale%' => $localeCode]);
    }

    /** @param array<string, string> $parameters */
    private function validationMessageOf(string $element, array $parameters = []): string
    {
        $field = $this->getElement($element, $parameters);
        while (!$field->hasClass('field') && !$field->hasClass('mb-3') && 'body' !== $field->getTagName()) {
            $field = $field->getParent();
        }

        $message = $field->find('css', '.invalid-feedback');
        if (null === $message) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $message->getText();
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        return array_merge($this->getSlugFormElements(), [
            'authors' => '[data-test-authors]',
            'categories' => '[data-test-categories]',
            'channels' => '[data-test-channels]',
            'content' => '[data-test-content="%locale%"]',
            'excerpt' => '[data-test-excerpt="%locale%"]',
            'featured' => '[data-test-featured]',
            'image_alt' => '[data-test-image-alt="%locale%"]',
            'image_file' => '[data-test-image-file]',
            'missing_alt' => '[data-test-missing-image-alt]',
            'remove_image' => '[data-test-remove-image]',
            'main_category' => '[data-test-main-category]',
            'tags' => '[data-test-tags]',
            'title' => '[data-test-title="%locale%"]',
        ]);
    }
}
