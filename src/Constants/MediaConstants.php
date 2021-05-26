<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Constants;

class MediaConstants
{
    public const TYPE_JPG = 'image/jpg';
    public const TYPE_JPEG = 'image/jpeg';
    public const TYPE_PNG = 'image/png';

    public const IMAGE = 'image';
    public const VIDEO = 'video';
    public const FILE = 'file';

    public const ALLOWED_MIME_TYPES = [
        self::TYPE_JPG,
        self::TYPE_JPEG,
        self::TYPE_PNG,
    ];
}
