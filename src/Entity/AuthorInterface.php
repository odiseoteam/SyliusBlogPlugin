<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\SlugAwareInterface;
use Sylius\Resource\Model\ToggleableInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;

interface AuthorInterface extends
    ResourceInterface,
    CodeAwareInterface,
    ToggleableInterface,
    TranslatableInterface,
    SlugAwareInterface,
    ImageAwareInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    /** @return list<array{network: string, url: string}> */
    public function getSocialLinks(): array;

    /** @param list<array{network: string, url: string}> $socialLinks */
    public function setSocialLinks(array $socialLinks): void;

    public function getAdminUser(): ?AdminUserInterface;

    public function setAdminUser(?AdminUserInterface $adminUser): void;

    public function getJobTitle(): ?string;

    public function setJobTitle(?string $jobTitle): void;

    public function getBio(): ?string;

    public function setBio(?string $bio): void;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): void;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): void;

    /** @return AuthorTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
