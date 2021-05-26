<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use Esites\SymfonyExtrasBundle\ValueObject\Collections\AbstractCollection;

class NodeTranslationInterfaceCollection extends AbstractCollection
{
    public function getClassOfElement(): string
    {
        return NodeTranslationInterface::class;
    }
}
