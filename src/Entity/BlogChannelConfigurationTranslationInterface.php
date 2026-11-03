<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslationInterface;

interface BlogChannelConfigurationTranslationInterface extends ResourceInterface, TranslationInterface
{
    public function getId(): ?int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;
}
