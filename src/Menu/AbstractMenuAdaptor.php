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
        ?MenuItem $parent = null,
        ?Request $request = null
    ): void {
        if (!$parent instanceof MenuItem) {
            return;
        }

        if ($parent->getRoute() !== $this->getParentRoute()) {
            return;
        }

        $menuItem = new TopMenuItem($menu);
        $menuItem
            ->setLabel($this->getMenuLabel())
            ->setUniqueId($this->getMenuUniqueId())
            ->setParent($parent)
        ;

        $route = $this->getMenuRoute();

        if (is_string($route)) {
            $menuItem->setRoute($route);
        }

        $this->checkIsActive(
            $menuItem,
            $request
        );

        $children[] = $menuItem;
    }

    private function checkIsActive(
        MenuItem $menuItem,
        ?Request $request
    ): void {
        $isActive = $this->isActive(
            $menuItem,
            $request
        );

        if ($isActive) {
            $this->setIsActive($menuItem);
        }
    }

    private function setIsActive(MenuItem $menuItem): void
    {
        $menuItem->setActive(true);
        $parent = $menuItem->getParent();

        if ($parent instanceof MenuItem) {
            $this->setIsActive($parent);
        }
    }

    private function isActive(
        MenuItem $menuItem,
        ?Request $request
    ): bool {
        if (!$request instanceof Request) {
            return false;
        }

        $activeRoutes = $this->getActiveRoutes();
        $activeRoutes[] = $menuItem->getRoute();

        foreach ($activeRoutes as $activeRoute) {
            if (!is_string($activeRoute)) {
                continue;
            }

            $isActive = $request->attributes->get('_route') === $activeRoute;

            if ($isActive) {
                return true;
            }
        }

        return false;
    }

    abstract public function getMenuRoute(): ?string;

    abstract public function getMenuLabel(): string;

    abstract public function getMenuUniqueId(): string;

    protected function getActiveRoutes(): array
    {
        return [];
    }

    protected function getParentRoute(): string
    {
        return 'KunstmaanAdminBundle_modules';
    }
}
