<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use Swift_Attachment;

class AttachmentCollection extends AbstractCollection
{
    public function getClassOfElement(): string
    {
        return Swift_Attachment::class;
    }
}
