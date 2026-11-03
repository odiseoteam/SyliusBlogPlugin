<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Resource\Model\AbstractTranslation;

class BlogChannelConfigurationTranslation extends AbstractTranslation implements BlogChannelConfigurationTranslationInterface
{
    protected ?int $id = null;

    protected ?string $title = null;

    protected ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
