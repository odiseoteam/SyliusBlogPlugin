<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\TimestampableTrait;
use Sylius\Resource\Model\TranslatableTrait;
use Sylius\Resource\Model\TranslationInterface;

class Article implements ArticleInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    protected ?int $id = null;

    protected ?string $code = null;

    protected string $state = self::STATE_DRAFT;

    protected ?\DateTimeInterface $publishedAt = null;

    protected bool $featured = false;

    protected bool $commentsEnabled = true;

    protected ?ArticleCategoryInterface $mainCategory = null;

    /** @var Collection<array-key, ChannelInterface> */
    protected Collection $channels;

    /** @var Collection<array-key, ArticleCategoryInterface> */
    protected Collection $categories;

    /** @var Collection<array-key, ArticleTagInterface> */
    protected Collection $tags;

    /** @var Collection<array-key, ArticleAuthorInterface> */
    protected Collection $articleAuthors;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->channels = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->articleAuthors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): void
    {
        $this->featured = $featured;
    }

    public function isCommentsEnabled(): bool
    {
        return $this->commentsEnabled;
    }

    public function setCommentsEnabled(bool $commentsEnabled): void
    {
        $this->commentsEnabled = $commentsEnabled;
    }

    public function getMainCategory(): ?ArticleCategoryInterface
    {
        return $this->mainCategory;
    }

    public function setMainCategory(?ArticleCategoryInterface $mainCategory): void
    {
        $this->mainCategory = $mainCategory;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(ChannelInterface $channel): void
    {
        $this->channels->removeElement($channel);
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function hasCategory(ArticleCategoryInterface $category): bool
    {
        return $this->categories->contains($category);
    }

    public function addCategory(ArticleCategoryInterface $category): void
    {
        if (!$this->hasCategory($category)) {
            $this->categories->add($category);
        }
    }

    public function removeCategory(ArticleCategoryInterface $category): void
    {
        $this->categories->removeElement($category);
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function hasTag(ArticleTagInterface $tag): bool
    {
        return $this->tags->contains($tag);
    }

    public function addTag(ArticleTagInterface $tag): void
    {
        if (!$this->hasTag($tag)) {
            $this->tags->add($tag);
        }
    }

    public function removeTag(ArticleTagInterface $tag): void
    {
        $this->tags->removeElement($tag);
    }

    public function getArticleAuthors(): Collection
    {
        return $this->articleAuthors;
    }

    public function hasArticleAuthor(ArticleAuthorInterface $articleAuthor): bool
    {
        return $this->articleAuthors->contains($articleAuthor);
    }

    public function addArticleAuthor(ArticleAuthorInterface $articleAuthor): void
    {
        if (!$this->hasArticleAuthor($articleAuthor)) {
            $this->articleAuthors->add($articleAuthor);
            $articleAuthor->setArticle($this);
        }
    }

    public function removeArticleAuthor(ArticleAuthorInterface $articleAuthor): void
    {
        if ($this->articleAuthors->removeElement($articleAuthor)) {
            $articleAuthor->setArticle(null);
        }
    }

    public function getAuthors(): array
    {
        $articleAuthors = $this->articleAuthors->toArray();
        usort(
            $articleAuthors,
            static fn (ArticleAuthorInterface $a, ArticleAuthorInterface $b): int => $a->getPosition() <=> $b->getPosition(),
        );

        $authors = [];
        foreach ($articleAuthors as $articleAuthor) {
            $author = $articleAuthor->getAuthor();
            if (null !== $author) {
                $authors[] = $author;
            }
        }

        return $authors;
    }

    public function getTitle(): ?string
    {
        return $this->getTranslation()->getTitle();
    }

    public function setTitle(?string $title): void
    {
        $this->getTranslation()->setTitle($title);
    }

    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    public function getExcerpt(): ?string
    {
        return $this->getTranslation()->getExcerpt();
    }

    public function setExcerpt(?string $excerpt): void
    {
        $this->getTranslation()->setExcerpt($excerpt);
    }

    public function getContent(): ?string
    {
        return $this->getTranslation()->getContent();
    }

    public function setContent(?string $content): void
    {
        $this->getTranslation()->setContent($content);
    }

    public function getMetaTitle(): ?string
    {
        return $this->getTranslation()->getMetaTitle();
    }

    public function setMetaTitle(?string $metaTitle): void
    {
        $this->getTranslation()->setMetaTitle($metaTitle);
    }

    public function getMetaDescription(): ?string
    {
        return $this->getTranslation()->getMetaDescription();
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->getTranslation()->setMetaDescription($metaDescription);
    }

    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var ArticleTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ArticleTranslationInterface
    {
        return new ArticleTranslation();
    }
}
