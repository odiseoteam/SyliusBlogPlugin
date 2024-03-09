<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Factory;

use Odiseo\BlogBundle\Factory\ArticleCommentFactoryInterface;
use Odiseo\BlogBundle\Model\ArticleCommentInterface as BaseArticleCommentInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCommentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ArticleCommentFactory implements ArticleCommentFactoryInterface
{
    public function __construct(
        private ArticleCommentFactoryInterface $decoratedFactory,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function createNew(): object
    {
        return $this->decoratedFactory->createNew();
    }

    public function createNewWithArticleOrComment(
        string $articleId,
        string $commentId = null,
    ): BaseArticleCommentInterface {
        /** @var ArticleCommentInterface $articleComment */
        $articleComment = $this->decoratedFactory->createNewWithArticleOrComment($articleId, $commentId);

        $token = $this->tokenStorage->getToken();

        if ($token instanceof TokenInterface) {
            $shopUser = $token->getUser();

            if ($shopUser instanceof ShopUserInterface) {
                $articleComment->setAuthor($shopUser);
            }
        }

        return $articleComment;
    }
}
