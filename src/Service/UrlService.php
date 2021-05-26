<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UrlService
{
    private EntityManagerInterface $entityManager;

    private RouterInterface $router;


    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function getAbsolutePathUrl(
        AbstractPage $page,
        array $parameters = []
    ): string {
        return $this->getUrl($page, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function getAbsoluteUrl(
        AbstractPage $page,
        array $parameters = []
    ): string {
        return $this->getUrl($page, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getRelativePathUrl(
        AbstractPage $page,
        array $parameters = []
    ): string {
        return $this->getUrl($page, $parameters, UrlGeneratorInterface::RELATIVE_PATH);
    }

    public function getNetworkPathUrl(
        AbstractPage $page,
        array $parameters = []
    ): string {
        return $this->getUrl($page, $parameters, UrlGeneratorInterface::NETWORK_PATH);
    }

    private function getUrl(
        AbstractPage $page,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_URL
    ): string {
        /** @var NodeTranslationRepository $nodeTranslationRepository */
        $nodeTranslationRepository = $this->entityManager->getRepository(NodeTranslation::class);
        $nodeTranslation = $nodeTranslationRepository->getNodeTranslationFor($page);

        if (!$nodeTranslation instanceof NodeTranslation) {
            throw new NotFoundHttpException(
                'Nodetranslation from ' . get_class($page) . ' with ID ' . $page->getId() . ' not found'
            );
        }

        $parameters['url'] = $nodeTranslation->getUrl();

        return $this->router->generate(
            '_slug',
            $parameters,
            $referenceType
        );
    }
}
