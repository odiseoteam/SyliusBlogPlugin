<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\BlogBundle\Doctrine\ORM\ArticleRepositoryInterface as BaseArticleRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\ChannelInterface;

interface ArticleRepositoryInterface extends BaseArticleRepositoryInterface
{
    public function createByChannelQueryBuilder(string $channelCode): QueryBuilder;

    public function createByCategoryAndChannelQueryBuilder(
        string $categorySlug,
        ?string $localeCode,
        string $channelCode,
    ): QueryBuilder;

    public function createByAuthorAndChannelQueryBuilder(
        ChannelInterface $channel,
        string $locale,
        string $authorUsername,
    ): QueryBuilder;

    public function findOneBySlugAndChannel(string $slug, ?string $localeCode, string $channelCode): ?ArticleInterface;

    public function findByChannel(string $channelCode): Pagerfanta;

    public function findByCategoryAndChannel(
        string $categorySlug,
        ?string $localeCode,
        string $channelCode,
    ): Pagerfanta;

    public function findByAuthorAndChannel(
        ChannelInterface $channel,
        string $locale,
        string $authorUsername,
    ): Pagerfanta;

    public function findLatestByChannel(ChannelInterface $channel, string $locale, int $count): array;
}
