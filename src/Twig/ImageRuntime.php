<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Symfony\Component\Asset\Packages;
use Twig\Extension\RuntimeExtensionInterface;

final class ImageRuntime implements RuntimeExtensionInterface
{
    public const PLACEHOLDER = 'bundles/odiseosyliusblogplugin/images/placeholder.svg';

    /** The same one the Sylius admin shows for users without an avatar. */
    public const AVATAR_PLACEHOLDER = 'bundles/syliusui/images/avatar.png';

    public function __construct(
        private CacheManager $cacheManager,
        private Packages $packages,
    ) {
    }

    public function url(ArticleInterface|ArticleCategoryInterface|AuthorInterface $subject, string $filter): string
    {
        $path = $subject->getImage()?->getPath();
        if (null !== $path) {
            return $this->cacheManager->getBrowserPath($path, $filter);
        }

        return $this->packages->getUrl($subject instanceof AuthorInterface ? self::AVATAR_PLACEHOLDER : self::PLACEHOLDER);
    }

    /** The article falls back to its title; categories and authors use their name. */
    public function alt(ArticleInterface|ArticleCategoryInterface|AuthorInterface $subject): string
    {
        if ($subject instanceof ArticleInterface) {
            return $subject->getImageAlt() ?: $subject->getTitle() ?? '';
        }

        return $subject->getName() ?? '';
    }
}
