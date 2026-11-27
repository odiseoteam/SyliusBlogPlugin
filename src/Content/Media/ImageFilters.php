<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Media;

/** The filters of LiipImagine the images of the content are drawn with, and the paths they take. */
final class ImageFilters
{
    /** Storage path: folders and a file name with its extension, never "..". */
    public const PATH = '~^[\w-]+(?:/[\w-]+)*\.[A-Za-z0-9]+$~';

    /** Width of each filter of the content, smallest first. They shrink to that width, never enlarge. */
    public const CONTENT = [
        640 => 'odiseo_blog_content_640',
        960 => 'odiseo_blog_content_960',
        1280 => 'odiseo_blog_content_1280',
        1920 => 'odiseo_blog_content_1920',
    ];

    /** Square thumbnails of the galleries. */
    public const GALLERY = 'odiseo_blog_gallery';

    public const GALLERY_SIZE = 600;

    /** The filter for a width: the smallest one at least as wide, or the largest one. */
    public static function forWidth(int $width): string
    {
        foreach (self::CONTENT as $filterWidth => $filter) {
            if ($filterWidth >= $width) {
                return $filter;
            }
        }

        return self::CONTENT[array_key_last(self::CONTENT)];
    }

    /** @phpstan-assert-if-true string $path */
    public static function isPath(mixed $path): bool
    {
        return is_string($path) && 1 === preg_match(self::PATH, $path);
    }
}
