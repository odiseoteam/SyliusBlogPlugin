<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Media;

use Odiseo\SyliusBlogPlugin\Command\PruneMediaCommand;
use Odiseo\SyliusBlogPlugin\Entity\ContentMedia;
use Odiseo\SyliusBlogPlugin\Media\MediaPruner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class MediaPrunerTest extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $article = $this->article('PRUNE_POST', 'prune-post');
        $article->setContent((string) json_encode(['type' => 'doc', 'content' => [
            ['type' => 'image', 'attrs' => ['path' => 'pr/un/used.jpg']],
            ['type' => 'gallery', 'attrs' => ['images' => [['path' => 'pr/un/in-gallery.jpg']]]],
        ]]));
        $this->entityManager->persist($article);

        $this->media('pr/un/used.jpg', '-30 days');
        $this->media('pr/un/in-gallery.jpg', '-30 days');
        $this->media('pr/un/unused.jpg', '-30 days');
        $this->media('pr/un/recent.jpg', '-1 day');
        $this->entityManager->flush();
    }

    public function testItRemovesOnlyTheOldFilesNoContentUses(): void
    {
        self::assertSame(['pr/un/unused.jpg'], $this->pruner()->prune(new \DateTimeImmutable('-7 days')));

        $this->entityManager->clear();
        self::assertSame(['pr/un/used.jpg', 'pr/un/in-gallery.jpg', 'pr/un/recent.jpg'], $this->paths());
    }

    public function testADryRunRemovesNothing(): void
    {
        self::assertSame(['pr/un/unused.jpg'], $this->pruner()->prune(new \DateTimeImmutable('-7 days'), true));

        $this->entityManager->clear();
        self::assertCount(4, $this->paths());
    }

    public function testTheCommandListsWhatItRemoves(): void
    {
        $command = self::getContainer()->get('odiseo_blog.command.prune_media');
        self::assertInstanceOf(PruneMediaCommand::class, $command);
        $tester = new CommandTester($command);

        self::assertSame(Command::SUCCESS, $tester->execute(['--dry-run' => true, '--days' => '0']));
        self::assertStringContainsString('pr/un/unused.jpg', $tester->getDisplay());
        self::assertStringContainsString('pr/un/recent.jpg', $tester->getDisplay());
        self::assertStringContainsString('2 unused files would be deleted.', $tester->getDisplay());

        self::assertSame(Command::INVALID, $tester->execute(['--days' => 'week']));
    }

    private function pruner(): MediaPruner
    {
        $pruner = self::getContainer()->get('odiseo_blog.media.pruner');
        self::assertInstanceOf(MediaPruner::class, $pruner);

        return $pruner;
    }

    private function media(string $path, string $uploaded): void
    {
        $media = new ContentMedia();
        $media->setPath($path);
        (new \ReflectionProperty(ContentMedia::class, 'createdAt'))->setValue($media, new \DateTime($uploaded));
        $this->entityManager->persist($media);
    }

    /** @return list<string|null> */
    private function paths(): array
    {
        return array_map(
            static fn (ContentMedia $media): ?string => $media->getPath(),
            $this->entityManager->getRepository(ContentMedia::class)->findBy([], ['id' => 'ASC']),
        );
    }
}
