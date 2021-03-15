<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Odiseo\BlogBundle\Model\ArticleComment as BaseArticleComment;
use Sylius\Component\Core\Model\ShopUserInterface;

class ArticleComment extends BaseArticleComment implements ArticleCommentInterface
{
    /** @var ShopUserInterface|null */
    protected $author;

    /**
     * {@inheritdoc}
     */
    public function getAuthor(): ?ShopUserInterface
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor(?ShopUserInterface $author): void
    {
        $this->author = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        $author = $this->getAuthor();
        if ($author instanceof ShopUserInterface) {
            return $author->getUsername();
        }

        return $this->getEmail();
    }
}
