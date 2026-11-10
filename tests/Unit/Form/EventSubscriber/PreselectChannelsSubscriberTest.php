<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Form\EventSubscriber;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Form\EventSubscriber\PreselectChannelsSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

#[CoversClass(PreselectChannelsSubscriber::class)]
final class PreselectChannelsSubscriberTest extends TestCase
{
    private Channel $web;

    private Channel $mobile;

    protected function setUp(): void
    {
        $this->web = new Channel();
        $this->mobile = new Channel();
    }

    public function testANewArticleStartsInEveryChannel(): void
    {
        $article = new Article();

        $this->preselect($article);

        self::assertSame([$this->web, $this->mobile], $article->getChannels()->toArray());
    }

    public function testChannelsAlreadyChosenAreKept(): void
    {
        $article = new Article();
        $article->addChannel($this->mobile);

        $this->preselect($article);

        self::assertSame([$this->mobile], $article->getChannels()->toArray());
    }

    public function testAStoredArticleIsLeftAsItIs(): void
    {
        $article = new Article();
        (new \ReflectionProperty(Article::class, 'id'))->setValue($article, 7);

        $this->preselect($article);

        self::assertTrue($article->getChannels()->isEmpty());
    }

    private function preselect(mixed $data): void
    {
        /** @var RepositoryInterface<ChannelInterface>&\PHPUnit\Framework\MockObject\Stub $repository */
        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('findAll')->willReturn([$this->web, $this->mobile]);

        (new PreselectChannelsSubscriber($repository))->preselectChannels(
            new FormEvent($this->createStub(FormInterface::class), $data),
        );
    }
}
