<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Resource\Model\ResourceInterface;

interface ArticleAuthorInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getArticle(): ?ArticleInterface;

    public function setArticle(?ArticleInterface $article): void;

    public function getAuthor(): ?AuthorInterface;

    public function setAuthor(?AuthorInterface $author): void;

    public function getPosition(): int;

    public function setPosition(int $position): void;
}
