<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use Esites\SymfonyExtrasBundle\ValueObject\Collections\AbstractCollection;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

class NodeTranslationCollection extends AbstractCollection
{
    public function getClassOfElement(): string
    {
        return NodeTranslation::class;
    }
}
