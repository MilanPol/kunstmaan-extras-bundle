<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject;

class SwitchLanguageValueObject
{
    private string $language;

    private string $name;

    private string $url;

    public function __construct(
        string $language,
        string $name,
        string $url
    ) {
        $this->language = $language;
        $this->name = $name;
        $this->url = $url;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): SwitchLanguageValueObject
    {
        $this->language = $language;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SwitchLanguageValueObject
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): SwitchLanguageValueObject
    {
        $this->url = $url;

        return $this;
    }
}
