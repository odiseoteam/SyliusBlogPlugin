<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameIt(string $name): void;

    public function setSlug(string $slug): void;

    public function generateSlug(): void;

    public function attachAvatar(string $path): void;

    public function addSocialLink(string $network, string $url): void;

    public function removeSocialLink(int $position): void;

    public function countSocialLinks(): int;

    public function getCodeValidationMessage(): string;

    public function getSlugValidationMessage(): string;
}
