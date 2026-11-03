<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Odiseo\SyliusBlogPlugin\Menu\AdminMenuListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

#[CoversClass(AdminMenuListener::class)]
final class AdminMenuListenerTest extends TestCase
{
    public function testTheBlogGoesAfterMarketing(): void
    {
        $menu = $this->menu(['catalog', 'marketing', 'configuration']);

        (new AdminMenuListener())->addAdminMenuItems(new MenuBuilderEvent($this->factory(), $menu));

        self::assertSame(['catalog', 'marketing', 'blog', 'configuration'], array_keys($menu->getChildren()));
        self::assertSame(['configuration'], array_keys($menu->getChild('blog')?->getChildren() ?? []));
    }

    public function testTheBlogGoesLastWithoutMarketing(): void
    {
        $menu = $this->menu(['catalog']);

        (new AdminMenuListener())->addAdminMenuItems(new MenuBuilderEvent($this->factory(), $menu));

        self::assertSame(['catalog', 'blog'], array_keys($menu->getChildren()));
    }

    /** @param list<string> $children */
    private function menu(array $children): ItemInterface
    {
        $menu = $this->factory()->createItem('root');
        foreach ($children as $child) {
            $menu->addChild($child);
        }

        return $menu;
    }

    private function factory(): FactoryInterface
    {
        return new MenuFactory();
    }
}
