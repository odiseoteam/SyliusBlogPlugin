<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class SetArticleAuthorListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage
    ) {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $token = $this->tokenStorage->getToken();

        if ($entity instanceof ArticleInterface && $token instanceof TokenInterface) {
            $user = $token->getUser();

            if ($user instanceof AdminUserInterface) {
                $entity->setAuthor($user);
            }
        }
    }
}
