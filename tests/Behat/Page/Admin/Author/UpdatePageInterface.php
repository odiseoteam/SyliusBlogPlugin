<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Author;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function nameIt(string $name): void;

    public function isCodeDisabled(): bool;

    public function addSocialLink(string $network, string $url): void;

    public function removeSocialLink(int $position): void;

    public function countSocialLinks(): int;

    public function removeAvatar(): void;
}
