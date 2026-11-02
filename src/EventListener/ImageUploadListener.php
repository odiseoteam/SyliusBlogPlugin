<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\EventListener;

use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/** Stores the new file of an article, category or author image before the owner is saved. */
final class ImageUploadListener
{
    public function __construct(
        private ImageUploaderInterface $uploader,
    ) {
    }

    public function uploadImage(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, ImageAwareInterface::class);

        $image = $subject->getImage();
        if (null === $image) {
            return;
        }

        $this->uploader->upload($image);

        // Without a file or a stored path there is nothing to show, and the path column can't be empty.
        if (null === $image->getPath()) {
            $subject->setImage(null);
        }
    }
}
