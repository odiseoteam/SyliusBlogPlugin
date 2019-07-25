<?php

namespace Odiseo\SyliusBlogPlugin\EventListener;

use Odiseo\SyliusBlogPlugin\Model\ArticleInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SetArticleAuthorListener
{
    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $token = $this->tokenStorage->getToken();

        if($entity instanceOf ArticleInterface && $token) {
            $user = $token->getUser();

            if ($user instanceof AdminUserInterface) {
                $entity->setAuthor($user);
            }
        }
    }
}
