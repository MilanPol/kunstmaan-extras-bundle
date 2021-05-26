<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataObject;

class LanguageTranslationDataObject
{
    private array $languages = [];

    public function addLanguage(
        string $languageCode,
        string $languageName
    ): LanguageTranslationDataObject {
        $this->languages[$languageCode] = $languageName;

        return $this;
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }
}
