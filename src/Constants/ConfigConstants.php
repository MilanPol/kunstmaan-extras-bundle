<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Constants;

class ConfigConstants
{
    public const PREFIX_KEY = 'esites_kunstmaan_extras';

    public const MAILER_USER = 'mailer_user';
    public const MAILER_NAME = 'mailer_name';

    public static function getParameterKeyName(string $key): string
    {
        return static::PREFIX_KEY . '.' . $key;
    }

    public static function getConfiguration(): array
    {
        return [
            static::MAILER_USER,
            static::MAILER_NAME,
        ];
    }
}
