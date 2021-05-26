<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataObject;

class LanguageTranslationDataObject
{
    /**
     * @var array<string,string>
     */
    private array $languages = [];

    public function addLanguage(
        string $languageCode,
        string $languageName
    ): LanguageTranslationDataObject {
        $this->languages[$languageCode] = $languageName;

        return $this;
    }

    /**
     * @return array<string,string>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
