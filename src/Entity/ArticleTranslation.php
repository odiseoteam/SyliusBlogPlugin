<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Resource\Model\AbstractTranslation;

class ArticleTranslation extends AbstractTranslation implements ArticleTranslationInterface
{
    protected ?int $id = null;

    protected ?string $title = null;

    protected ?string $slug = null;

    protected ?string $excerpt = null;

    protected ?string $content = null;

    protected ?string $metaTitle = null;

    protected ?string $metaDescription = null;

    protected ?string $imageAlt = null;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getImageAlt(): ?string
    {
        return $this->imageAlt;
    }

    public function setImageAlt(?string $imageAlt): void
    {
        $this->imageAlt = $imageAlt;
    }
}
