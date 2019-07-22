<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Model;

use Sylius\Component\Core\Model\ShopUserInterface;
use Odiseo\BlogBundle\Model\ArticleComment as BaseArticleComment;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
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
    public function getUsername()
    {
        if($this->getAuthor()) {
            return $this->getAuthor()->getUsername();
        }

        return $this->getEmail();
    }
}
