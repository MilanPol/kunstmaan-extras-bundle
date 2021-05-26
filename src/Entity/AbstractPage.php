<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\AbstractPage as KumaAbstractPage;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractPage extends KumaAbstractPage implements NodeTranslationInterface, SearchViewTemplateInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\NodeBundle\Entity\NodeTranslation")
     */
    private ?NodeTranslation $nodeTranslation = null;

    public function getNodeTranslation(): ?NodeTranslation
    {
        return $this->nodeTranslation;
    }

    public function setNodeTranslation(?NodeTranslation $nodeTranslation): AbstractPage
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    public function getSearchView(): string
    {
        return '@EsitesKunstmaanExtras\index-page-parts.html.twig';
    }
}
