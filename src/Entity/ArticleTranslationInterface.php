<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\SlugAwareInterface;
use Sylius\Resource\Model\TranslationInterface;

interface ArticleTranslationInterface extends ResourceInterface, TranslationInterface, SlugAwareInterface
{
    public function getId(): ?int;

    public function getTitle(): ?string;

    public function setTitle(?string $title): void;

    public function getExcerpt(): ?string;

    public function setExcerpt(?string $excerpt): void;

    /** Tiptap JSON, stored as sent by the editor. */
    public function getContent(): ?string;

    public function setContent(?string $content): void;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): void;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): void;

    public function getImageAlt(): ?string;

    public function setImageAlt(?string $imageAlt): void;
}
