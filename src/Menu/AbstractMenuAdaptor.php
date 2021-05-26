<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractMenuAdaptor implements MenuAdaptorInterface
{
    public function adaptChildren(
        MenuBuilder $menu,
        array &$children,
        MenuItem $parent = null,
        Request $request = null
    ): void {
        if (!$parent instanceof MenuItem) {
            return;
        }

        if ($parent->getRoute() !== 'KunstmaanAdminBundle_modules') {
            return;
        }

        $menuItem = new TopMenuItem($menu);
        $menuItem
            ->setRoute($this->getMenuRoute())
            ->setLabel($this->getMenuLabel())
            ->setUniqueId($this->getMenuUniqueId())
            ->setParent($parent)
        ;

        if ($this->isActive($menuItem, $request)) {
            $menuItem->setActive(true);
            $parent->setActive(true);
        }

        $children[] = $menuItem;
    }

    private function isActive(TopMenuItem $menuItem, ?Request $request = null): bool
    {
        if (!$request instanceof Request) {
            return false;
        }

        return $request->attributes->get('_route') === $menuItem->getRoute();
    }

    abstract public function getMenuRoute(): string;

    abstract public function getMenuLabel(): string;

    abstract public function getMenuUniqueId(): string;
}
