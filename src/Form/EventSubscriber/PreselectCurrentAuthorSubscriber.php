<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\EventSubscriber;

use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/** A new article is signed by the enabled author linked to the current administrator. */
final class PreselectCurrentAuthorSubscriber implements EventSubscriberInterface
{
    /** @param RepositoryInterface<AuthorInterface> $authorRepository */
    public function __construct(
        private readonly RepositoryInterface $authorRepository,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [FormEvents::PRE_SET_DATA => 'preselectAuthor'];
    }

    public function preselectAuthor(FormEvent $event): void
    {
        $article = $event->getData();
        if (!$article instanceof ArticleInterface || null !== $article->getId() || !$article->getArticleAuthors()->isEmpty()) {
            return;
        }

        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user instanceof AdminUserInterface) {
            return;
        }

        $author = $this->authorRepository->findOneBy(['adminUser' => $user, 'enabled' => true]);
        if ($author instanceof AuthorInterface) {
            $article->setAuthors([$author]);
        }
    }
}
