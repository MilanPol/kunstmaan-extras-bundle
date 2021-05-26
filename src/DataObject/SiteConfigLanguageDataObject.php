<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataObject;

class SiteConfigLanguageDataObject
{
    private array $siteConfigurationByLanguage = [];

    public function addSiteConfiguration(
        string $language,
        string $siteConfigurationClass
    ): SiteConfigLanguageDataObject {
        $this->siteConfigurationByLanguage[$language] = $siteConfigurationClass;

        return $this;
    }

    public function getSiteConfigurations(): array
    {
        return $this->siteConfigurationByLanguage;
    }
}
