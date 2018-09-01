<?php

namespace spec\Odiseo\SyliusBlogPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Odiseo\SyliusBlogPlugin\Model\Article;
use Odiseo\SyliusBlogPlugin\Model\ArticleInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
class ArticleSpec extends ObjectBehavior
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
