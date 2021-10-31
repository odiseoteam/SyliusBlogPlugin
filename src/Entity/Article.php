<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Odiseo\BlogBundle\Model\Article as BaseArticle;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\AdminUserInterface;

class Article extends BaseArticle implements ArticleInterface
{
    protected ?AdminUserInterface $author = null;

    /**
     * @psalm-var Collection<array-key, ChannelInterface>
     */
    protected Collection $channels;

    public function __construct()
    {
        parent::__construct();

        $this->channels = new ArrayCollection();
    }

    public function getAuthor(): ?AdminUserInterface
    {
        return $this->author;
    }

    public function setAuthor(?AdminUserInterface $author): void
    {
        $this->author = $author;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }
}
