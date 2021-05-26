<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture as DoctrineFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Esites\KunstmaanExtrasBundle\Helper\PageCreator;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use InvalidArgumentException;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractFixture extends DoctrineFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public static array $localeMap = [
        'nl' => 'nl_NL',
        'en' => 'en_GB',
        'fr' => 'fr_FR',
        'de' => 'de_DE',
    ];

    protected ContainerInterface $container;

    protected PageCreator $pageCreator;

    protected MediaCreatorService $mediaCreator;

    protected SlugifierInterface $slugifier;

    /**
     * @var array<int,FakerGenerator>
     */
    protected array $faker = [];

    /**
     * @var array<int,string>
     */
    protected array $locales = [];

    public function setContainer(?ContainerInterface $container = null): void
    {
        if (!$container instanceof ContainerInterface) {
            throw new InvalidArgumentException('This fixture requires a container');
        }

        $this->container = $container;

        $this->setServices();
        $this->setLocales();
    }

    private function setServices(): void
    {
        /** @var PageCreator $pageCreator */
        $pageCreator = $this->container->get(PageCreator::class);
        $this->pageCreator = $pageCreator;

        /** @var MediaCreatorService $mediaCreatorService */
        $mediaCreatorService = $this->container->get('kunstmaan_media.media_creator_service');
        $this->mediaCreator = $mediaCreatorService;

        /** @var Slugifier $slugifier */
        $slugifier = $this->container->get('kunstmaan_utilities.slugifier');
        $this->slugifier = $slugifier;
    }

    private function setLocales(): void
    {
        /** @var string $locales */
        $locales = $this->container->getParameter('requiredlocales');

        $this->locales = explode(
            '|',
            $locales
        );

        foreach ($this->locales as $locale) {
            $locale = static::$localeMap[$locale] ?? $locale;

            $generator = FakerFactory::create($locale);

            $this->faker[$locale] = $generator;
        }
    }

    protected function getRandomFile(): Media
    {
        return $this->getRandomByContentType('application/pdf');
    }

    protected function getRandomVideo(): Media
    {
        return $this->getRandomByContentType('remote/video');
    }

    protected function getRandomImage(): Media
    {
        return $this->getRandomByContentType('image/jpeg');
    }

    protected function getRandomByContentType(string $contentType): Media
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container->get('doctrine.orm.entity_manager');

        $folderRepository = $entityManager->getRepository(Folder::class);
        $folders = $folderRepository->findBy(
            [
                'rel' => Folder::allTypes(),
            ]
        );

        $mediaRepository = $entityManager->getRepository(Media::class);
        $media = $mediaRepository->findBy(
            [
                'contentType' => $contentType,
                'folder'      => $folders,
            ]
        );
        shuffle($media);

        return $media[0];
    }

    protected function getFaker(string $locale): FakerGenerator
    {
        $locale = self::$localeMap[$locale] ?? $locale;

        return $this->faker[$locale];
    }
}
