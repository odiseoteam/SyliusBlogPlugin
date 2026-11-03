<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\ChannelConfiguration;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function isBlogShown(): bool;

    public function hideBlog(): void;

    public function getPostsPerPage(): int;

    public function setPostsPerPage(int $postsPerPage): void;

    public function nameBlog(string $title, string $localeCode): void;

    public function getPostsPerPageValidationMessage(): string;
}
