<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Odiseo\BlogBundle\Model\ArticleCommentInterface as BaseArticleCommentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

interface ArticleCommentInterface extends BaseArticleCommentInterface
{
    public function getAuthor(): ?ShopUserInterface;

    public function setAuthor(?ShopUserInterface $author): void;

    public function getUsername(): ?string;
}
