<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $blog = $menu
            ->addChild('blog')
            ->setLabel('odiseo_sylius_blog_plugin.menu.admin.blog.header')
        ;

        $blog
            ->addChild('article_categories', ['route' => 'odiseo_blog_admin_article_category_index'])
            ->setLabel('odiseo_sylius_blog_plugin.menu.admin.blog.article_categories')
            ->setLabelAttribute('icon', 'list alternate')
        ;

        $blog
            ->addChild('articles', ['route' => 'odiseo_blog_admin_article_index'])
            ->setLabel('odiseo_sylius_blog_plugin.menu.admin.blog.articles')
            ->setLabelAttribute('icon', 'newspaper')
        ;

        $blog
            ->addChild('article_comments', ['route' => 'odiseo_blog_admin_article_comment_index'])
            ->setLabel('odiseo_sylius_blog_plugin.menu.admin.blog.article_comments')
            ->setLabelAttribute('icon', 'comment')
        ;
    }
}
