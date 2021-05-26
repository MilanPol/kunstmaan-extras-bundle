# E-sites Kunstmaan Extras Bundle 

Symfony bundle for use in Kunstmaan 5.x projects. 

## Installation

1. Add repository to your composer.json. [Make sure you added your SSH key](https://git.e-sites.nl/user/settings/ssh) to git.e-sites.nl.

    ```json
    ...
    "repositories": [
        {
          "type": "vcs",
          "url": "https://gitlab.e-sites.nl/e-sites/kunstmaan/extrasbundle.git"
        }
    ]
    ...
    ```

2. Require the bundle
    
    ```bash
    $ composer require esites/kunstmaan-extras-bundle
    ```    

## Features

### Custom fixtures

* **Activate admin user**
   
      This activates the generated admin user.
      This prevents you from having to change the password every time you run the fixtures. 
    

### Abstract classes for your convenience

* \Esites\KunstmaanExtrasBundle\Repository\AbstractPageRepository

    getQueryBuilder method that automatically inner joins on NodeTranslation, NodeVersion and Node. 
    
    Usage: simply extend your page repository with AbstractPageRepository
    
* \Esites\KunstmaanExtrasBundle\Entity\AbstractDefaultSiteConfig

    Default SiteConfig configuration. Simply extend and define the fields you're expecting in the siteConfig.
    Then extend that class for each language you have activated.
    
* \Esites\KunstmaanExtrasBundle\EventSubscriber\AbstractLanguageSubscriber

    When going multilanguage, you want visitors to get redirected to the right language, when there is no language in their url (this mostly happens when going to the main domain name).
    This subscriber redirects the visitor to the right language.
    
    Usage: Extend this subscriber and define all the languages available in `getAvailableLanguages()` (lowercase 2-letter codes)
    Also define the default language (lowercase 2-letter code) in `getDefaultLanguage()`
    
* \Esites\KunstmaanExtrasBundle\Menu\AbstractMenuAdaptor

    For each module you've created in Kunstmaan, extend this menu adaptor to add the module to the menu in the admin environment
    
    Usage: Extend the `AbstractMenuAdaptor` and define the three methods:
    * `getMenuRoute()`: The index route name, defined in the AdminListConfigurator
    * `getMenuLabel()`: The label of the menu item. Can be a translatable string.
    * `getMenuUniqueId()`: An ID for the menu item. Has to be unique.
    
* \Esites\KunstmaanExtrasBundle\Twig\AbstractSiteConfigExtension

    Twig extension to get the SiteConfig based on the current locale of the application in a twig template.
    
    Usage: Extend the `AbstractSiteConfigExtension` and define `getSiteConfigsByLanguage()`.
    After that, `get_site_config()` will be available in the twig template.
    
* \Esites\KunstmaanExtrasBundle\Twig\AbstractLanguageExtension

    Twig extension to build the language switcher in a twig template.
    This extension keeps into account switching to the right page in another language.
    
    Usage: Extend the `AbstractLanguageExtension` and define `getActiveLanguages()` and `getDefaultLanguage()`.
    After that, `get_switch_language_links()` will be available in the twig template.