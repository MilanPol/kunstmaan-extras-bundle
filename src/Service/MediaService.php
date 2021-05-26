<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Service;

use Esites\KunstmaanExtrasBundle\Constants\MediaConstants;
use Kunstmaan\MediaBundle\Entity\Media;

class MediaService
{
    public function getMediaType(?Media $media = null): ?string
    {
        if (!$media instanceof Media) {
            return null;
        }

        $contentType = $media->getContentType();

        switch ($contentType) {
            case strpos($contentType, MediaConstants::IMAGE) !== false:
                return MediaConstants::IMAGE;
            case strpos($contentType, MediaConstants::VIDEO) !== false:
                return MediaConstants::VIDEO;
            case strpos($contentType, MediaConstants::FILE) !== false:
                return MediaConstants::FILE;
            default:
                return null;
        }
    }
}
