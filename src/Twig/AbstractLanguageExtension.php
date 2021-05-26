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

        $switchLanguages = new SwitchLanguageValueObjectCollection();
        $languageTranslationDataObject = $this->getActiveLanguages();
        $activeLanguages = $languageTranslationDataObject->getLanguages();
        $currentLanguage = $this->getCurrentLanguage($request);
        $languages = [
            $currentLanguage => $activeLanguages[$currentLanguage],
        ];
        $languages = array_merge(
            $languages,
            $activeLanguages
        );

        foreach ($languages as $language => $name) {
            $this->addSwitchLanguage(
                $request,
                $switchLanguages,
                $language,
                $name
            );
        }

        return $switchLanguages;
    }

    private function addSwitchLanguage(
        Request $request,
        SwitchLanguageValueObjectCollection $switchLanguages,
        string $language,
        string $name
    ): void {
        if (!is_string($language)) {
            return;
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
            return;
        }
    }

    private function getCurrentLanguage(Request $request): string
    {
        $locale = $request->getLocale();

        if (!is_string($locale)) {
            return $this->getDefaultLanguage();
        }

        $activeLanguages = $this->getActiveLanguages()->getLanguages();

        $hasLocale = in_array(
            $locale,
            $activeLanguages,
            true
        );

        if ($hasLocale) {
            return $locale;
        }

        return $this->getDefaultLanguage();
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

        $languageNodeTranslation = $this->getLanguageNodeTranslation(
            $nodeTranslation,
            $language
        );

        $parameters['url'] = $languageNodeTranslation->getUrl();

        return $this->urlGenerator->generate(
            '_slug',
            $parameters
        );
    }

    /**
     * @throws NoNodeTranslationException
     */
    private function getLanguageNodeTranslation(
        NodeTranslation $nodeTranslation,
        string $language
    ): NodeTranslation {
        $node = $nodeTranslation->getNode();

        if (!$node instanceof Node) {
            throw new InvalidArgumentException(
                'Invalid node'
            );
        }

        $languageNodeTranslation = $node->getNodeTranslation($language);

        if (!$languageNodeTranslation instanceof NodeTranslation) {
            throw new NoNodeTranslationException(
                'Node translations not found for language ' . $language
            );
        }

        return $languageNodeTranslation;
    }
}
