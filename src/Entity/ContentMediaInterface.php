<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Entity;

use Sylius\Component\Core\Model\ImageInterface;

/** A file uploaded from the content editor. Removing it deletes the file and its thumbnails. */
interface ContentMediaInterface extends ImageInterface
{
    public function getCreatedAt(): \DateTimeInterface;
}
