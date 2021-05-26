<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Twig;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagePartExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_used_regions',
                [
                    $this,
                    'getUsedRegions',
                ]
            ),
        ];
    }

    public function getUsedRegions(
        HasPagePartsInterface $hasPageParts
    ): array {
        /** @var PagePartRefRepository $pagePartRefRepository */
        $pagePartRefRepository = $this->entityManager->getRepository(PagePartRef::class);

        $regions = $pagePartRefRepository
            ->createQueryBuilder('pagePartRef')
            ->select('DISTINCT pagePartRef.context')
            ->andWhere('pagePartRef.pageId = :pageId')
            ->setParameter(
                'pageId',
                $hasPageParts->getId()
            )
            ->andWhere('pagePartRef.pageEntityname = :pageClass')
            ->setParameter(
                'pageClass',
                ClassUtils::getClass($hasPageParts)
            )
            ->orderBy(
                'pagePartRef.context',
                Criteria::ASC
            )
            ->getQuery()
            ->getScalarResult()
        ;

        return array_column($regions, 'context');
    }
}
