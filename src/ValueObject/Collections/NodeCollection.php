<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use Esites\SymfonyExtrasBundle\ValueObject\Collections\AbstractCollection;
use Kunstmaan\NodeBundle\Entity\Node;

class NodeCollection extends AbstractCollection
{
    public function getClassOfElement(): string
    {
        return Node::class;
    }
}
