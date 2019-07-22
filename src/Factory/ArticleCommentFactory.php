<?php

namespace Odiseo\SyliusBlogPlugin\Factory;

use Odiseo\BlogBundle\Factory\ArticleCommentFactoryInterface;
use Odiseo\BlogBundle\Model\ArticleCommentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ArticleCommentFactory implements ArticleCommentFactoryInterface
{
    /** @var ArticleCommentFactoryInterface  */
    private $decoratedFactory;

    /** @var ShopUserInterface */
    private $shopUser;

    public function __construct(ArticleCommentFactoryInterface $decoratedFactory, TokenStorageInterface $tokenStorage)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->shopUser = $tokenStorage->getToken()?$tokenStorage->getToken()->getUser():null;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createNewWithArticleOrComment(string $articleId, string $commentId = null): ArticleCommentInterface
    {
        /** @var \Odiseo\SyliusBlogPlugin\Model\ArticleCommentInterface $articleComment */
        $articleComment = $this->decoratedFactory->createNewWithArticleOrComment($articleId, $commentId);

        if($this->shopUser instanceof ShopUserInterface) {
            $articleComment->setAuthor($this->shopUser);
        }

        return $articleComment;
    }
}
