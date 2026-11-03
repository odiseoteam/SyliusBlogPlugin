<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\EventListener;

use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfigurationInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/** The admin edits the configuration of any channel, so the first save creates it. */
final class ChannelConfigurationPersistListener
{
    public function __construct(private readonly ObjectManager $manager)
    {
    }

    public function persist(GenericEvent $event): void
    {
        $configuration = $event->getSubject();
        Assert::isInstanceOf($configuration, BlogChannelConfigurationInterface::class);

        $this->manager->persist($configuration);
    }
}
