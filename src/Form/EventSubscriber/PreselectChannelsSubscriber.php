<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\EventSubscriber;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/** A new resource starts in every channel. */
final class PreselectChannelsSubscriber implements EventSubscriberInterface
{
    /** @param RepositoryInterface<ChannelInterface> $channelRepository */
    public function __construct(private readonly RepositoryInterface $channelRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preselectChannels'];
    }

    public function preselectChannels(FormEvent $event): void
    {
        $resource = $event->getData();
        if (!$resource instanceof ChannelsAwareInterface || !$resource instanceof ResourceInterface) {
            return;
        }

        if (null !== $resource->getId() || !$resource->getChannels()->isEmpty()) {
            return;
        }

        foreach ($this->channelRepository->findAll() as $channel) {
            $resource->addChannel($channel);
        }
    }
}
