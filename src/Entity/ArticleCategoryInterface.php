<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Resource\Model\CodeAwareInterface;
use Sylius\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\SlugAwareInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslationInterface;

/** A node of the category tree. Gedmo keeps the root, left, right, level and sibling position. */
interface ArticleCategoryInterface extends
    ResourceInterface,
    CodeAwareInterface,
    TranslatableInterface,
    SlugAwareInterface,
    ImageAwareInterface
{
    public function getId(): ?int;

    public function isRoot(): bool;

    public function getRoot(): ?self;

    public function getParent(): ?self;

    public function setParent(?self $parent): void;

    /** @return Collection<array-key, ArticleCategoryInterface> */
    public function getChildren(): Collection;

    public function hasChild(self $category): bool;

    public function hasChildren(): bool;

    public function addChild(self $category): void;

    public function removeChild(self $category): void;

    public function getLeft(): ?int;

    public function getRight(): ?int;

    public function getLevel(): ?int;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getMetaTitle(): ?string;

    public function setMetaTitle(?string $metaTitle): void;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): void;

    /** @return ArticleCategoryTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
