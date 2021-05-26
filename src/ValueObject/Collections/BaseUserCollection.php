<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use Kunstmaan\AdminBundle\Entity\BaseUser;

class BaseUserCollection extends AbstractCollection
{
    public function getClassOfElement(): string
    {
        return BaseUser::class;
    }
}
