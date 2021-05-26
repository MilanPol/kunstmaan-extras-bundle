<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use Esites\KunstmaanExtrasBundle\ValueObject\SwitchLanguageValueObject;

class SwitchLanguageValueObjectCollection extends AbstractCollection
{
    public function getClassOfElement(): string
    {
        return SwitchLanguageValueObject::class;
    }
}
