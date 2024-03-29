<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Odiseo\BlogBundle\Model\ArticleComment as BaseArticleComment;
use Sylius\Component\Core\Model\ShopUserInterface;

class ArticleComment extends BaseArticleComment implements ArticleCommentInterface
{
    protected ?ShopUserInterface $author = null;

    public function getAuthor(): ?ShopUserInterface
    {
        return $this->author;
    }

    public function setAuthor(?ShopUserInterface $author): void
    {
        $this->author = $author;
    }

    public function getUsername(): ?string
    {
        $author = $this->getAuthor();
        if ($author instanceof ShopUserInterface) {
            /**
             * @phpstan-ignore-next-line
             */
            return $author->getUserIdentifier();
        }

        return $this->getEmail();
    }
}
