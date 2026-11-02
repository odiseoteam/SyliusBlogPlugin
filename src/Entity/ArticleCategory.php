<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Resource\Model\TranslatableTrait;
use Sylius\Resource\Model\TranslationInterface;

class ArticleCategory implements ArticleCategoryInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    protected ?int $id = null;

    protected ?string $code = null;

    protected ?ArticleCategoryInterface $root = null;

    protected ?ArticleCategoryInterface $parent = null;

    /** @var Collection<array-key, ArticleCategoryInterface> */
    protected Collection $children;

    protected ?int $left = null;

    protected ?int $right = null;

    protected ?int $level = null;

    protected ?int $position = null;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->children = new ArrayCollection();
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

    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    public function getRoot(): ?ArticleCategoryInterface
    {
        return $this->root;
    }

    public function getParent(): ?ArticleCategoryInterface
    {
        return $this->parent;
    }

    public function setParent(?ArticleCategoryInterface $parent): void
    {
        $this->parent = $parent;
        $parent?->addChild($this);
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function hasChild(ArticleCategoryInterface $category): bool
    {
        return $this->children->contains($category);
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function addChild(ArticleCategoryInterface $category): void
    {
        if (!$this->hasChild($category)) {
            $this->children->add($category);
        }

        if ($this !== $category->getParent()) {
            $category->setParent($this);
        }
    }

    public function removeChild(ArticleCategoryInterface $category): void
    {
        if ($this->hasChild($category)) {
            $category->setParent(null);
            $this->children->removeElement($category);
        }
    }

    public function getLeft(): ?int
    {
        return $this->left;
    }

    public function getRight(): ?int
    {
        return $this->right;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
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
        /** @var ArticleCategoryTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ArticleCategoryTranslationInterface
    {
        return new ArticleCategoryTranslation();
    }
}
