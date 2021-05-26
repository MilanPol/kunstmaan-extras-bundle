<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use Locale;

abstract class AbstractPageRepository extends EntityRepository
{
    public function getQueryBuilder(?string $locale = null): QueryBuilder
    {
        if (!is_string($locale)) {
            $locale = Locale::getDefault();
        }

        $pageHasNodeTranslatonInterface = is_subclass_of(
            $this->getClassName(),
            NodeTranslationInterface::class
        );

        if ($pageHasNodeTranslatonInterface) {
            $this->getNodeTranslationQueryBuilder($locale);
        }

        return $this->getLegacyQueryBuilder($locale);
    }

    private function getNodeTranslationQueryBuilder(?string $locale): QueryBuilder
    {
        return $this
            ->createQueryBuilder('page')
            ->addSelect('nodeTranslation')
            ->addSelect('node')
            ->innerJoin(
                'page.nodeTranslation',
                'nodeTranslation'
            )
            ->innerJoin(
                'nodeTranslation.node',
                'node',
            )
            ->where('nodeTranslation.online = 1')
            ->andWhere('node.deleted = 0')
            ->andWhere('nodeTranslation.lang = :lang')
            ->andWhere('nodeVersion.refEntityName = :refName')
            ->setParameter(
                'refName',
                $this->getClassName()
            )
            ->setParameter(
                'lang',
                $locale
            )
            ;
    }

    private function getLegacyQueryBuilder(?string $locale): QueryBuilder
    {
        return $this
            ->createQueryBuilder('page')
            ->innerJoin(
                'KunstmaanNodeBundle:NodeVersion',
                'nodeVersion',
                Join::WITH,
                'page.id = nodeVersion.refId'
            )
            ->innerJoin(
                'KunstmaanNodeBundle:NodeTranslation',
                'nodeTranslation',
                Join::WITH,
                'nodeTranslation.publicNodeVersion = nodeVersion.id'
            )
            ->innerJoin(
                'KunstmaanNodeBundle:Node',
                'node',
                Join::WITH,
                'nodeTranslation.node = node.id'
            )
            ->where('nodeTranslation.online = 1')
            ->andWhere('node.deleted = 0')
            ->andWhere('nodeTranslation.lang = :lang')
            ->andWhere('nodeVersion.refEntityName = :refName')
            ->setParameter(
                'refName',
                $this->getClassName()
            )
            ->setParameter(
                'lang',
                $locale
            )
        ;
    }
}
