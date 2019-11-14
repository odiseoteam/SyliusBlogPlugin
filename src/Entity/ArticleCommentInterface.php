<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Odiseo\BlogBundle\Model\ArticleCommentInterface as BaseArticleCommentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

interface ArticleCommentInterface extends BaseArticleCommentInterface
{
    /**
     * @return ShopUserInterface|null
     */
    public function getAuthor(): ?ShopUserInterface;

    /**
     * @param ShopUserInterface|null $author
     */
    public function setAuthor(?ShopUserInterface $author): void;

    /**
     * @return string|null
     */
    public function getUsername(): ?string;
}
