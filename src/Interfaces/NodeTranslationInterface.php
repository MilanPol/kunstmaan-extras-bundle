<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Interfaces;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;

interface NodeTranslationInterface
{
    public function setNodeTranslation(?NodeTranslation $nodeTranslation): NodeTranslationInterface;
}
