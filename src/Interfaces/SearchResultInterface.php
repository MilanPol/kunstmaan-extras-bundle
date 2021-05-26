<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Interfaces;

use Kunstmaan\MediaBundle\Entity\Media;

interface SearchResultInterface
{
    public function getSearchImage(): ?Media;

    public function getSearchDescription(): ?string;
}
