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
             * @psalm-suppress DeprecatedMethod
             * @phpstan-ignore-next-line
             */
            return method_exists($author, 'getUserIdentifier') ? $author->getUserIdentifier() : $author->getUsername();
        }

        return $this->getEmail();
    }
}
