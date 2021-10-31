<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\BlogBundle\Doctrine\ORM\ArticleRepository as BaseArticleRepository;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\ChannelInterface;

class ArticleRepository extends BaseArticleRepository implements ArticleRepositoryInterface
{
    public function createByChannelQueryBuilder(?string $channelCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.channels', 'channels')
            ->andWhere('o.enabled = true')
            ->andWhere('channels.code = :channelCode')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('channelCode', $channelCode)
        ;
    }

    public function createByCategoryAndChannelQueryBuilder(
        string $categorySlug,
        ?string $localeCode,
        string $channelCode
    ): QueryBuilder {
        return $this->createByChannelQueryBuilder($channelCode)
            ->leftJoin('o.categories', 'category')
            ->leftJoin('category.translations', 'categoryTranslation')
            ->andWhere('categoryTranslation.locale = :localeCode')
            ->andWhere('categoryTranslation.slug = :slug')
            ->setParameter('localeCode', $localeCode)
            ->setParameter('slug', $categorySlug)
        ;
    }

    public function createByAuthorAndChannelQueryBuilder(
        ChannelInterface $channel,
        string $locale,
        string $authorUsername
    ): QueryBuilder {
        return $this->createByChannelQueryBuilder($channel->getCode())
            ->addSelect('translation')
            ->addSelect('author')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->leftJoin('o.author', 'author')
            ->andWhere('author.username = :username')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setParameter('locale', $locale)
            ->setParameter('username', $authorUsername)
        ;
    }

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

    public function findByChannel(string $channelCode): Pagerfanta
    {
        return $this->getPaginator($this->createByChannelQueryBuilder($channelCode));
    }

    public function findByCategoryAndChannel(string $categorySlug, ?string $localeCode, string $channelCode): Pagerfanta
    {
        return $this->getPaginator(
            $this->createByCategoryAndChannelQueryBuilder(
                $categorySlug,
                $localeCode,
                $channelCode
            )
        );
    }

    public function findByAuthorAndChannel(
        ChannelInterface $channel,
        string $locale,
        string $authorUsername
    ): Pagerfanta {
        return $this->getPaginator($this->createByAuthorAndChannelQueryBuilder($channel, $locale, $authorUsername));
    }

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
