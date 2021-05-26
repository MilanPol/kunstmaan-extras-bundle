<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Esites\KunstmaanExtrasBundle\DataObject\SiteConfigLanguageDataObject;
use Esites\KunstmaanExtrasBundle\Entity\AbstractDefaultSiteConfig;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

abstract class AbstractSiteConfigExtension extends AbstractExtension
{
    protected RequestStack $requestStack;

    protected EntityManagerInterface $entityManager;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager
    ) {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    abstract public function getSiteConfigsByLanguage(): SiteConfigLanguageDataObject;

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_site_config',
                [
                    $this,
                    'getSiteConfig',
                ]
            ),
        ];
    }

    public function getSiteConfig(
        ?string $language = null
    ): AbstractDefaultSiteConfig {
        if (!is_string($language)) {
            $request = $this->requestStack->getCurrentRequest();

            if (!$request instanceof Request) {
                throw new InvalidArgumentException('Invalid request');
            }

            $language = $request->getLocale();
        }

        $siteConfigurationLanguage = $this->getSiteConfigsByLanguage();
        $siteConfigurations = $siteConfigurationLanguage->getSiteConfigurations();

        if (!isset($siteConfigurations[$language])) {
            throw new InvalidArgumentException('Invalid language');
        }

        /** @var class-string $siteConfigClassName */
        $siteConfigClassName = $siteConfigurations[$language];

        $siteConfigRepository = $this->entityManager->getRepository($siteConfigClassName);
        $siteConfig = $siteConfigRepository->findOneBy([]);

        if (!$siteConfig instanceof AbstractDefaultSiteConfig) {
            throw new InvalidArgumentException('Siteconfig does not exist');
        }

        return $siteConfig;
    }
}
