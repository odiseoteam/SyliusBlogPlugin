<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\EventSubscriber;

use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/** The main category of an article is always one of its categories. */
final class AddMainCategoryToCategoriesSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Before the validation listener, which runs at 0.
        return [FormEvents::POST_SUBMIT => ['addMainCategory', 10]];
    }

    public function addMainCategory(FormEvent $event): void
    {
        $article = $event->getData();
        if (!$article instanceof ArticleInterface) {
            return;
        }

        $mainCategory = $article->getMainCategory();
        if (null !== $mainCategory) {
            $article->addCategory($mainCategory);
        }
    }
}
