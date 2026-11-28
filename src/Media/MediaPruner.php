<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Media;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBlogPlugin\Entity\ContentMediaInterface;

/** Removes the uploaded files no content uses. Sylius deletes each file and its thumbnails with its record. */
final class MediaPruner
{
    private const BATCH_SIZE = 100;

    /**
     * @param iterable<MediaUsageProviderInterface> $usageProviders
     * @param class-string<ContentMediaInterface> $mediaClass
     */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly iterable $usageProviders,
        private readonly string $mediaClass,
    ) {
    }

    /**
     * Only files uploaded before `$before` are considered: newer ones may belong to a content still being written.
     *
     * @return list<string> The paths of the files removed, or that would be with `$dryRun`.
     */
    public function prune(\DateTimeInterface $before, bool $dryRun = false): array
    {
        $used = [];
        foreach ($this->usageProviders as $provider) {
            foreach ($provider->getUsedPaths() as $path) {
                $used[$path] = true;
            }
        }

        /** @var iterable<ContentMediaInterface> $candidates */
        $candidates = $this->manager->createQueryBuilder()
            ->select('media')
            ->from($this->mediaClass, 'media')
            ->where('media.createdAt < :before')
            ->setParameter('before', $before)
            ->orderBy('media.id')
            ->getQuery()
            ->toIterable()
        ;

        $pruned = [];
        foreach ($candidates as $media) {
            $path = (string) $media->getPath();
            if (isset($used[$path])) {
                continue;
            }

            $pruned[] = $path;
            if (!$dryRun) {
                $this->manager->remove($media);
                if (0 === count($pruned) % self::BATCH_SIZE) {
                    $this->manager->flush();
                }
            }
        }

        if (!$dryRun) {
            $this->manager->flush();
        }

        return $pruned;
    }
}
