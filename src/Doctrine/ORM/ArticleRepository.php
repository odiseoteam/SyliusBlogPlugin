<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Doctrine\ORM;

use Odiseo\BlogBundle\Doctrine\ORM\ArticleRepository as BaseArticleRepository;
use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBlogPlugin\Model\ArticleInterface;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\ChannelInterface;

class ArticleRepository extends BaseArticleRepository implements ArticleRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function createByChannelQueryBuilder(string $channelCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.channels', 'channels')
            ->andWhere('o.enabled = true')
            ->andWhere('channels.code = :channelCode')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channelCode', $channelCode)
        ;
    }

    /**
     * @inheritdoc
     */
    public function createByCategoryAndChannelQueryBuilder(string $categorySlug, ?string $localeCode, string $channelCode): QueryBuilder
    {
        return $this->createByChannelQueryBuilder($channelCode)
            ->leftJoin('o.categories', 'category')
            ->leftJoin('category.translations', 'categoryTranslation')
            ->andWhere('categoryTranslation.locale = :localeCode')
            ->andWhere('categoryTranslation.slug = :slug')
            ->setParameter('localeCode', $localeCode)
            ->setParameter('slug', $categorySlug)
        ;
    }

    /**
     * @inheritdoc
     */
    public function findOneBySlugAndChannel(string $slug, ?string $localeCode, string $channelCode): ?ArticleInterface
    {
        return $this->createByChannelQueryBuilder($channelCode)
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.locale = :localeCode')
            ->andWhere('translation.slug = :slug')
            ->setParameter('localeCode', $localeCode)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @inheritdoc
     */
    public function findByChannel(string $channelCode): Pagerfanta
    {
        return $this->getPaginator($this->createByChannelQueryBuilder($channelCode));
    }

    /**
     * @inheritdoc
     */
    public function findByCategoryAndChannel(string $categorySlug, ?string $localeCode, string $channelCode): Pagerfanta
    {
        return $this->getPaginator($this->createByCategoryAndChannelQueryBuilder($categorySlug, $localeCode, $channelCode));
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestByChannel(ChannelInterface $channel, string $locale, int $count): array
    {
        return $this->createByChannelQueryBuilder($channel->getCode())
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('locale', $locale)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }
}
