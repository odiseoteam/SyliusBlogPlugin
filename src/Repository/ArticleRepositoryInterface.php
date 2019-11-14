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
    /**
     * @param string $channelCode
     * @return QueryBuilder
     */
    public function createByChannelQueryBuilder(string $channelCode): QueryBuilder;

    /**
     * @param string $categorySlug
     * @param string|null $localeCode
     * @param string $channelCode
     * @return QueryBuilder
     */
    public function createByCategoryAndChannelQueryBuilder(string $categorySlug, ?string $localeCode, string $channelCode): QueryBuilder;

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param string $authorUsername
     * @return QueryBuilder
     */
    public function createByAuthorAndChannelQueryBuilder(ChannelInterface $channel, string $locale, string $authorUsername): QueryBuilder;

    /**
     * @param string $slug
     * @param string|null $localeCode
     * @param string $channelCode
     * @return ArticleInterface|null
     */
    public function findOneBySlugAndChannel(string $slug, ?string $localeCode, string $channelCode): ?ArticleInterface;

    /**
     * @param string $channelCode
     * @return Pagerfanta
     */
    public function findByChannel(string $channelCode): Pagerfanta;

    /**
     * @param string $categorySlug
     * @param string|null $localeCode
     * @param string $channelCode
     * @return Pagerfanta
     */
    public function findByCategoryAndChannel(string $categorySlug, ?string $localeCode, string $channelCode): Pagerfanta;

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param string $authorUsername
     * @return Pagerfanta
     */
    public function findByAuthorAndChannel(ChannelInterface $channel, string $locale, string $authorUsername): Pagerfanta;

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param int $count
     * @return array
     */
    public function findLatestByChannel(ChannelInterface $channel, string $locale, int $count): array;
}
