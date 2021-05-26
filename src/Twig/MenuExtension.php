<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Twig;

use Kunstmaan\MenuBundle\Entity\MenuItem;
use Kunstmaan\MenuBundle\Twig\MenuTwigExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    private MenuTwigExtension $menuTwigExtension;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        MenuTwigExtension $menuTwigExtension,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->menuTwigExtension = $menuTwigExtension;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_structured_menu',
                [
                    $this,
                    'getStructuredMenu',
                ]
            ),
        ];
    }

    public function getStructuredMenu(
        string $menuName,
        string $locale
    ): array {
        $items = $this->menuTwigExtension->getMenuItems(
            $menuName,
            $locale
        );
        $mainItems = [];
        $subItems = [];

        foreach ($items as $item) {
            $item['children'] = [];

            if ($item['type'] === MenuItem::TYPE_PAGE_LINK && !empty($item['nodeTranslation'])) {
                $item['title'] = $item['title'] ?? $item['nodeTranslation']['title'];
                $item['url'] = $this->urlGenerator->generate(
                    '_slug',
                    [
                        'url' => $item['nodeTranslation']['url'],
                    ]
                );
            }

            if (empty($item['parent'])) {
                $mainItems[] = $item;

                continue;
            }

            if (!isset($subItems[$item['parent']['id']])) {
                $subItems[$item['parent']['id']] = [];
            }

            $subItems[$item['parent']['id']][] = $item;
        }

        foreach ($mainItems as $key => $mainItem) {
            $this->addSubItems(
                $mainItem,
                $subItems
            );

            $mainItems[$key] = $mainItem;
        }

        return $mainItems;
    }

    private function addSubItems(
        array &$item,
        array $subItems
    ): void {
        if (!isset($subItems[$item['id']])) {
            return;
        }

        foreach ($subItems[$item['id']] as $subItem) {
            $this->addSubItems(
                $subItem,
                $subItems
            );

            $item['children'][] = $subItem;
        }
    }
}
