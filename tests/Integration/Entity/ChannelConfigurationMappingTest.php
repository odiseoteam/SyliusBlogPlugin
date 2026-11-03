<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Entity;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Odiseo\SyliusBlogPlugin\Configuration\BlogChannelConfigurationProviderInterface;
use Odiseo\SyliusBlogPlugin\Entity\BlogChannelConfiguration;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class ChannelConfigurationMappingTest extends DatabaseTestCase
{
    public function testAConfigurationIsStoredWithItsTranslations(): void
    {
        $configuration = $this->configuration('WEB');
        $configuration->setEnabled(false);
        $configuration->setPostsPerPage(12);
        $configuration->setTitle('Journal');
        $configuration->setDescription('Stories from the shop.');
        $this->entityManager->persist($configuration);
        $this->entityManager->flush();

        $configuration = $this->reload(BlogChannelConfiguration::class, $configuration->getId());

        self::assertSame('WEB', $configuration->getChannel()?->getCode());
        self::assertFalse($configuration->isEnabled());
        self::assertSame(12, $configuration->getPostsPerPage());
        self::assertSame('Journal', $configuration->getTranslation('en_US')->getTitle());
        self::assertSame('Stories from the shop.', $configuration->getTranslation('en_US')->getDescription());
    }

    public function testAChannelHasOneConfiguration(): void
    {
        $first = $this->configuration('WEB');
        $this->entityManager->persist($first);
        $this->entityManager->flush();

        $second = new BlogChannelConfiguration();
        $second->setChannel($first->getChannel());
        $second->setPostsPerPage(9);
        $this->entityManager->persist($second);

        $this->expectException(UniqueConstraintViolationException::class);
        $this->entityManager->flush();
    }

    public function testDeletingTheChannelDeletesItsConfiguration(): void
    {
        $configuration = $this->configuration('WEB');
        $this->entityManager->persist($configuration);
        $this->entityManager->flush();
        $id = $configuration->getId();
        $channel = $configuration->getChannel();
        self::assertNotNull($channel);

        $this->entityManager->remove($channel);
        $this->entityManager->flush();
        $this->entityManager->clear();

        self::assertNull($this->entityManager->find(BlogChannelConfiguration::class, $id));
    }

    public function testTheProviderFallsBackToTheDefaults(): void
    {
        $provider = self::getContainer()->get('odiseo_blog.provider.channel_configuration');
        self::assertInstanceOf(BlogChannelConfigurationProviderInterface::class, $provider);

        $channel = $this->channel('MOBILE');
        $this->entityManager->flush();
        $defaults = $provider->getConfiguration($channel);

        self::assertNull($defaults->getId());
        self::assertSame($channel, $defaults->getChannel());
        self::assertTrue($defaults->isEnabled());
        self::assertSame(9, $defaults->getPostsPerPage());

        $stored = $this->configuration('WEB');
        $this->entityManager->persist($stored);
        $this->entityManager->flush();
        $channel = $stored->getChannel();
        self::assertNotNull($channel);

        self::assertSame($stored, $provider->getConfiguration($channel));
    }

    private function configuration(string $channelCode): BlogChannelConfiguration
    {
        $configuration = new BlogChannelConfiguration();
        $configuration->setCurrentLocale('en_US');
        $configuration->setFallbackLocale('en_US');
        $configuration->setChannel($this->channel($channelCode));
        $configuration->setPostsPerPage(9);

        return $configuration;
    }
}
