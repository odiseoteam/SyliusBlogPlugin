<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Model;

use Sylius\Component\Core\Model\ShopUserInterface;
use Odiseo\BlogBundle\Model\ArticleCommentInterface as BaseArticleCommentInterface;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
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
    public function getUsername();
}
