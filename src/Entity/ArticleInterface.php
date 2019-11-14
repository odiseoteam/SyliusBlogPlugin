<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Odiseo\BlogBundle\Model\ArticleInterface as BaseArticleInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Core\Model\AdminUserInterface;

interface ArticleInterface extends BaseArticleInterface, ChannelsAwareInterface
{
    /**
     * @return AdminUserInterface|null
     */
    public function getAuthor(): ?AdminUserInterface;

    /**
     * @param AdminUserInterface|null $author
     */
    public function setAuthor(?AdminUserInterface $author): void;
}
