<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\ToggleableTrait;
use Sylius\Resource\Model\TranslatableTrait;
use Sylius\Resource\Model\TranslationInterface;

class BlogChannelConfiguration implements BlogChannelConfigurationInterface
{
    use ToggleableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    protected ?int $id = null;

    protected ?ChannelInterface $channel = null;

    protected ?int $postsPerPage = null;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getPostsPerPage(): ?int
    {
        return $this->postsPerPage;
    }

    public function setPostsPerPage(?int $postsPerPage): void
    {
        $this->postsPerPage = $postsPerPage;
    }

    public function getTitle(): ?string
    {
        return $this->getTranslation()->getTitle();
    }

    public function setTitle(?string $title): void
    {
        $this->getTranslation()->setTitle($title);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var BlogChannelConfigurationTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): BlogChannelConfigurationTranslationInterface
    {
        return new BlogChannelConfigurationTranslation();
    }
}
