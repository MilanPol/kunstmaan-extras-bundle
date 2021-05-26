<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Twig;

use Esites\KunstmaanExtrasBundle\DataObject\LanguageTranslationDataObject;
use Esites\KunstmaanExtrasBundle\ValueObject\Collections\SwitchLanguageValueObjectCollection;
use Esites\KunstmaanExtrasBundle\ValueObject\SwitchLanguageValueObject;
use InvalidArgumentException;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Exception\NoNodeTranslationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

abstract class AbstractLanguageExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;

    protected RequestStack $requestStack;

    protected UrlGeneratorInterface $urlGenerator;


    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    abstract public function getActiveLanguages(): LanguageTranslationDataObject;

    abstract public function getDefaultLanguage(): string;


    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_switch_language_links',
                [
                    $this,
                    'getSwitchLanguageLinks',
                ]
            ),
        ];
    }

    public function getSwitchLanguageLinks(): SwitchLanguageValueObjectCollection
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new InvalidArgumentException(
                'Invalid request'
            );
        }

        $languageTranslationDataObject = $this->getActiveLanguages();
        $activeLanguages = $languageTranslationDataObject->getLanguages();
        $currentLanguage = $this->getCurrentLanguage($request);

        $languages[$currentLanguage] = $activeLanguages[$currentLanguage];
        $languages = array_merge(
            $languages,
            $activeLanguages
        );

        $switchLanguages = new SwitchLanguageValueObjectCollection();

        foreach ($languages as $language => $name) {
            if (!is_string($language)) {
                continue;
            }

            try {
                $url = $this->getSwitchLanguageUrl(
                    $request,
                    $language
                );

                $switchLanguages->addElement(
                    new SwitchLanguageValueObject(
                        $language,
                        $name,
                        $url
                    )
                );
            } catch (NoNodeTranslationException $noNodeTranslationException) {
                continue;
            }
        }

        return $switchLanguages;
    }

    private function getCurrentLanguage(Request $request): string
    {
        $locale = $request->getLocale();

        if (!is_string($locale)) {
            return $this->getDefaultLanguage();
        }

        $activeLanguages = $this->getActiveLanguages();

        if (!isset($activeLanguages[$locale])) {
            return $this->getDefaultLanguage();
        }

        return $locale;
    }

    /**
     * @throws NoNodeTranslationException
     */
    private function getSwitchLanguageUrl(
        Request $request,
        string $language
    ): string {
        $nodeTranslation = $request->attributes->get('_nodeTranslation');

        $parameters = $request->query->all();
        $parameters['_locale'] = $language;

        if (!$nodeTranslation instanceof NodeTranslation) {
            return $this->urlGenerator->generate(
                $request->attributes->get('_route'),
                $parameters
            );
        }

        $node = $nodeTranslation->getNode();

        if (!$node instanceof Node) {
            throw new InvalidArgumentException(
                'Invalid node'
            );
        }

        $languageNodeTranslation = $node->getNodeTranslation($language);

        if (!$languageNodeTranslation instanceof NodeTranslation) {
            throw new NoNodeTranslationException(
                'Node translations not found for language '.$language
            );
        }

        $parameters['url'] = $nodeTranslation->getUrl();

        return $this->urlGenerator->generate(
            '_slug',
            $parameters
        );
    }
}
