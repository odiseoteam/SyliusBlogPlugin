<?php

declare(strict_types=1);

namespace spec\Odiseo\SyliusBlogPlugin\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Channel;

final class ArticleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Article::class);
    }

    function it_extends_base_article()
    {
        $this->shouldHaveType(\Odiseo\BlogBundle\Model\Article::class);
    }

    function it_implements_article_interface(): void
    {
        $this->shouldImplement(ArticleInterface::class);
    }

    function it_allows_access_via_properties(): void
    {
        $channel = new Channel();

        $this->getChannels()->shouldHaveCount(0);

        $this->addChannel($channel);
        $this->hasChannel($channel)->shouldReturn(true);
        $this->getChannels()->shouldHaveCount(1);
    }
}
