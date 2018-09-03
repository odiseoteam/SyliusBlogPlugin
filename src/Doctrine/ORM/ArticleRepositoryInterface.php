<?php

namespace Odiseo\SyliusBlogPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Odiseo\BlogBundle\Doctrine\ORM\ArticleRepositoryInterface as BaseArticleRepositoryInterface;
use Odiseo\SyliusBlogPlugin\Model\ArticleInterface;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\ChannelInterface;

interface ArticleRepositoryInterface extends BaseArticleRepositoryInterface
{
    /**
     * @param string $channelCode
     *
     * @return QueryBuilder
     */
    public function createByChannelQueryBuilder(string $channelCode): QueryBuilder;

    /**
     * @param string $categorySlug
     * @param null|string $localeCode
     * @param string $channelCode
     *
     * @return QueryBuilder
     */
    public function createByCategoryAndChannelQueryBuilder(string $categorySlug, ?string $localeCode, string $channelCode): QueryBuilder;

    /**
     * @param string $slug
     * @param null|string $localeCode
     * @param string $channelCode
     *
     * @return null|ArticleInterface
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySlugAndChannel(string $slug, ?string $localeCode, string $channelCode): ?ArticleInterface;

    /**
     * @param string $channelCode
     *
     * @return Pagerfanta
     */
    public function findByChannel(string $channelCode): Pagerfanta;

    /**
     * @param string $categorySlug
     * @param null|string $localeCode
     * @param string $channelCode
     *
     * @return Pagerfanta
     */
    public function findByCategoryAndChannel(string $categorySlug, ?string $localeCode, string $channelCode): Pagerfanta;

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param int $count
     *
     * @return array
     */
    public function findLatestByChannel(ChannelInterface $channel, string $locale, int $count): array;
}