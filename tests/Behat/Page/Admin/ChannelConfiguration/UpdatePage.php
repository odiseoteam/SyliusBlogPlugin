<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ChannelConfiguration;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function isBlogShown(): bool
    {
        return $this->getElement('enabled')->isChecked();
    }

    public function hideBlog(): void
    {
        $this->getElement('enabled')->uncheck();
    }

    public function getPostsPerPage(): int
    {
        return (int) $this->getElement('posts_per_page')->getValue();
    }

    public function setPostsPerPage(int $postsPerPage): void
    {
        $this->getElement('posts_per_page')->setValue((string) $postsPerPage);
    }

    public function nameBlog(string $title, string $localeCode): void
    {
        $this->getElement('title', ['%locale%' => $localeCode])->setValue($title);
    }

    public function getPostsPerPageValidationMessage(): string
    {
        return (string) $this->getElement('posts_per_page')->getParent()->find('css', '.invalid-feedback')?->getText();
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        /** @var array<string, string> $elements */
        $elements = array_merge(parent::getDefinedElements(), [
            'enabled' => '[data-test-enabled]',
            'posts_per_page' => '[data-test-posts-per-page]',
            'title' => '[data-test-title="%locale%"]',
        ]);

        return $elements;
    }
}
