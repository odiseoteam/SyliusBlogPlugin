<?php

namespace Odiseo\SyliusBlogPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();

        $blog = $menu
            ->addChild('blog')
            ->setLabel('odiseo_sylius_blog.menu.admin.blog.header')
        ;

        $blog
            ->addChild('article_categories', ['route' => 'odiseo_blog_admin_article_category_index'])
            ->setLabel('odiseo_sylius_blog.menu.admin.blog.article_categories')
            ->setLabelAttribute('icon', 'list alternate')
        ;

        $blog
            ->addChild('articles', ['route' => 'odiseo_blog_admin_article_index'])
            ->setLabel('odiseo_sylius_blog.menu.admin.blog.articles')
            ->setLabelAttribute('icon', 'newspaper')
        ;
    }
}
