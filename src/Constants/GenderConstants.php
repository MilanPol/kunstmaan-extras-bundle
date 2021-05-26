<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Constants;

class GenderConstants
{
    public const MALE = 'MALE';
    public const FEMALE = 'FEMALE';
    public const OTHER = 'OTHER';

    public static function getGenders(): array
    {
        return [
            static::MALE,
            static::FEMALE,
            static::OTHER,
        ];
    }
}
