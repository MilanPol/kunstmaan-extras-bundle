<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Helper;

use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use InvalidArgumentException;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\SeoBundle\Entity\Seo;

class PageCreator
{
    /**
     * Username that is used for creating pages
     */
    protected const ADMIN_USERNAME = 'admin';

    protected PageCreatorService $pageCreatorService;

    protected array $requiredLocales = [];

    public function __construct(
        PageCreatorService $pageCreatorService,
        string $requiredLocales
    ) {
        $this->pageCreatorService = $pageCreatorService;
        $this->requiredLocales = explode(
            '|',
            $requiredLocales
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function create(
        string $type,
        PageCreatorConfigInterface $config,
        ?Node $parent = null,
        ?callable $closure = null
    ): Node {
        /** @var AbstractPage $subject */
        $subject = new $type();
        $subject->setTitle((string) $config->getTitle());

        $translations = [];
        foreach ($this->requiredLocales as $locale) {
            $translations[] = [
                'language' => $locale,
                'callback' => static function (
                    AbstractPage $page,
                    NodeTranslation $nodeTranslation,
                    Seo $seo
                ) use (
                    $config,
                    $locale,
                    $closure
                ): void {
                    $nodeTranslation->setTitle((string) $config->getTitle($locale));
                    $nodeTranslation->setLang($locale);
                    $nodeTranslation->setSlug($config->getSlug($locale));
                    $nodeTranslation->setWeight($config->getWeight());

                    $page->setPageTitle((string) $config->getTitle($locale));

                    if ($page instanceof NodeTranslationInterface) {
                        $page->setNodeTranslation($nodeTranslation);
                    }

                    if ($closure !== null) {
                        $closure(
                            $page,
                            $nodeTranslation
                        );
                    }
                },
            ];
        }

        return $this->pageCreatorService->createPage(
            $subject,
            $translations,
            [
                'parent'             => $parent,
                'page_internal_name' => $config->getInternalName(),
                'set_online'         => $config->getOnline(),
                'hidden_from_nav'    => $config->getHiddenFromNav(),
                'creator'            => $config->getCreatorObject() ?? $config->getCreator() ?? static::ADMIN_USERNAME,
            ]
        );
    }

    public function recursivelyCreatePages(
        array $pages,
        int $weight,
        Node $parent,
        int $depth = 3,
        ?callable $closure = null
    ): void {
        if ($depth <= 0) {
            throw new InvalidArgumentException(
                'The maximum depth at which you can create content pages has been reached.'
            );
        }

        foreach ($pages as $pageData) {
            $weight++;

            $pageParent = $this->createPageWithParent(
                $parent,
                $pageData,
                $weight,
                $closure
            );

            if (!empty($pageData['subs'])) {
                $this->recursivelyCreatePages(
                    $pageData['subs'],
                    $weight,
                    $pageParent,
                    $depth - 1,
                    $closure
                );
            }
        }
    }

    public function createPageWithParent(
        Node $parent,
        array $pageData,
        int $weight,
        ?callable $closure = null
    ): Node {
        if (!isset($pageData['type']) || !class_exists($pageData['type'])) {
            throw new InvalidArgumentException('Missing page type');
        }

        $type = $pageData['type'];

        $config = (new PageCreatorConfig())
            ->setTitle($pageData['options']['title'] ?? null)
            ->setSlug($pageData['options']['slug'] ?? null)
            ->setTitles($pageData['options']['titles'] ?? null)
            ->setSlugs($pageData['options']['slugs'] ?? null)
            ->setHiddenFromNav($pageData['options']['hidden_from_nav'] ?? false)
            ->setInternalName($pageData['options']['page_internal_name'] ?? null)
            ->setCreator($pageData['options']['creator'] ?? static::ADMIN_USERNAME)
            ->setOnline($pageData['options']['set_online'] ?? true)
            ->setWeight($weight)
        ;

        return $this->create(
            $type,
            $config,
            $parent,
            $closure
        );
    }
}
