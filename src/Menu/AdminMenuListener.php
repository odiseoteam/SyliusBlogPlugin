<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $blog = $menu
            ->addChild('blog')
            ->setLabel('odiseo_blog.menu.admin.header')
            ->setLabelAttribute('icon', 'tabler:news')
        ;

        $blog
            ->addChild('tags', ['route' => 'odiseo_blog_admin_article_tag_index', 'extras' => ['routes' => [
                ['route' => 'odiseo_blog_admin_article_tag_create'],
                ['route' => 'odiseo_blog_admin_article_tag_update'],
            ]]])
            ->setLabel('odiseo_blog.menu.admin.tags')
            ->setLabelAttribute('icon', 'tabler:tags')
        ;

        $blog
            ->addChild('configuration', ['route' => 'odiseo_blog_admin_channel_configuration_index', 'extras' => ['routes' => [
                ['route' => 'odiseo_blog_admin_channel_configuration_update'],
            ]]])
            ->setLabel('odiseo_blog.menu.admin.configuration')
            ->setLabelAttribute('icon', 'tabler:settings')
        ;

        $this->placeAfter($menu, 'blog', 'marketing');
    }

    /** Keeps the item at the end when the reference item doesn't exist. */
    private function placeAfter(ItemInterface $menu, string $name, string $reference): void
    {
        $order = array_keys($menu->getChildren());
        $position = array_search($reference, $order, true);
        if (false === $position) {
            return;
        }

        $order = array_values(array_diff($order, [$name]));
        array_splice($order, $position + 1, 0, [$name]);

        $menu->reorderChildren($order);
    }
}
