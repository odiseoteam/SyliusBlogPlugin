<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\SlugAwareInterface;
use Sylius\Resource\Model\TimestampableInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;

interface ArticleInterface extends
    ResourceInterface,
    CodeAwareInterface,
    TimestampableInterface,
    TranslatableInterface,
    ChannelsAwareInterface,
    SlugAwareInterface
{
    public const STATE_DRAFT = 'draft';

    public const STATE_PUBLISHED = 'published';

    public const STATE_ARCHIVED = 'archived';

    public function getId(): ?int;

    public function getState(): string;

    public function setState(string $state): void;

    public function getPublishedAt(): ?\DateTimeInterface;

    public function setPublishedAt(?\DateTimeInterface $publishedAt): void;

    public function isFeatured(): bool;

    public function setFeatured(bool $featured): void;

    public function isCommentsEnabled(): bool;

    public function setCommentsEnabled(bool $commentsEnabled): void;

    public function getMainCategory(): ?ArticleCategoryInterface;

    public function setMainCategory(?ArticleCategoryInterface $mainCategory): void;

    /** @return Collection<array-key, ArticleCategoryInterface> */
    public function getCategories(): Collection;

    public function hasCategory(ArticleCategoryInterface $category): bool;

    public function addCategory(ArticleCategoryInterface $category): void;

    public function removeCategory(ArticleCategoryInterface $category): void;

    /** @return Collection<array-key, ArticleTagInterface> */
    public function getTags(): Collection;

    public function hasTag(ArticleTagInterface $tag): bool;

    public function addTag(ArticleTagInterface $tag): void;

    public function removeTag(ArticleTagInterface $tag): void;

    /** @return Collection<array-key, ArticleAuthorInterface> */
    public function getArticleAuthors(): Collection;

    public function hasArticleAuthor(ArticleAuthorInterface $articleAuthor): bool;

    public function addArticleAuthor(ArticleAuthorInterface $articleAuthor): void;

    public function removeArticleAuthor(ArticleAuthorInterface $articleAuthor): void;

    /** @return list<AuthorInterface> In byline order. */
    public function getAuthors(): array;

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

    /** @return ArticleTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
