<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

class ArticleAuthor implements ArticleAuthorInterface
{
    protected ?int $id = null;

    protected ?ArticleInterface $article = null;

    protected ?AuthorInterface $author = null;

    protected int $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?ArticleInterface
    {
        return $this->article;
    }

    public function setArticle(?ArticleInterface $article): void
    {
        $this->article = $article;
    }

    public function getAuthor(): ?AuthorInterface
    {
        return $this->author;
    }

    public function setAuthor(?AuthorInterface $author): void
    {
        $this->author = $author;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
