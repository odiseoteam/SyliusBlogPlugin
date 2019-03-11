<?php

namespace Odiseo\SyliusBlogPlugin\Model;

use Odiseo\BlogBundle\Model\ArticleInterface as BaseArticleInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;

interface ArticleInterface extends BaseArticleInterface, ChannelsAwareInterface
{
}
