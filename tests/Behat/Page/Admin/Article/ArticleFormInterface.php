<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article;

interface ArticleFormInterface
{
    public function titleIt(string $title, string $localeCode): void;

    public function setSlug(string $slug, string $localeCode): void;

    public function generateSlug(string $localeCode): void;

    public function setExcerpt(string $excerpt, string $localeCode): void;

    public function setContent(string $content, string $localeCode): void;

    public function writeInContentEditor(string $text, string $localeCode): void;

    public function useContentEditorTool(string $label, string $localeCode): void;

    public function addVideoToContent(string $url, string $localeCode): void;

    public function addProductToContent(string $name, string $localeCode): void;

    public function addImageToContent(string $file, string $alt, string $localeCode): void;

    /** @param list<string> $files */
    public function addGalleryToContent(array $files, string $localeCode): void;

    public function getContentEditorStats(string $localeCode): string;

    public function markAsFeatured(): void;

    public function isAvailableInChannel(string $channelName): bool;

    public function makeUnavailableInChannel(string $channelName): void;

    public function setMainCategory(string $name): void;

    public function addCategory(string $name): void;

    public function addTag(string $name): void;

    public function attachImage(string $path): void;

    public function describeImage(string $alt, string $localeCode): void;

    public function hasMissingAltWarning(): bool;

    public function getLocalesWithoutAlt(): string;

    public function addAuthor(string $name): void;

    public function removeAuthor(string $name): void;

    public function getCodeValidationMessage(): string;

    public function getSlugValidationMessage(string $localeCode): string;

    public function getContentValidationMessage(string $localeCode): string;
}
