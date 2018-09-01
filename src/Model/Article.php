<?php

namespace Odiseo\SyliusBlogPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Odiseo\BlogBundle\Model\Article as BaseArticle;
use Sylius\Component\Channel\Model\ChannelInterface;

class Article extends BaseArticle implements ArticleInterface
{
    /** @var Collection|ChannelInterface[] */
    protected $channels;

    public function __construct()
    {
        parent::__construct();

        $this->channels = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * @inheritdoc
     */
    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * @param ChannelInterface $channel
     */
    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }
}