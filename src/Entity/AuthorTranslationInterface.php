<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslationInterface;

interface AuthorTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getId(): ?int;

    public function getJobTitle(): ?string;

    public function setJobTitle(?string $jobTitle): void;

    public function getBio(): ?string;

    public function setBio(?string $bio): void;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): void;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): void;
}
