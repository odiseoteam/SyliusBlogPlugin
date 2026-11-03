<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\ToggleableInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;

/** The blog settings of a channel. */
interface BlogChannelConfigurationInterface extends
    ResourceInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public function getId(): ?int;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getPostsPerPage(): ?int;

    public function setPostsPerPage(?int $postsPerPage): void;

    public function getTitle(): ?string;

    public function setTitle(?string $title): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    /** @return BlogChannelConfigurationTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
