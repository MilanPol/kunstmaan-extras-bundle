<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Esites\KunstmaanExtrasBundle\DataObject\AbstractFilterDataObject;
use Kunstmaan\NodeBundle\Entity\Node;

abstract class AbstractFilteredPageRepository extends AbstractPageRepository
{
    /**
     * If you need to apply a filter or any other queries to the QueryBuilder
     * Overwrite this method to do so
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applyFilterQueryBuilder(
        QueryBuilder $queryBuilder,
        ?AbstractFilterDataObject $abstractFilterDataObject
    ): void {
    }

    public function getPagesByOverviewPageQueryBuilder(
        ?Node $rootNode,
        ?AbstractFilterDataObject $abstractFilterDataObject,
        ?string $locale = null
    ): QueryBuilder {
        $queryBuilder = $this->getQueryBuilder($locale);
        $queryBuilder
            ->orderBy(
                'nodeTranslation.title',
                Criteria::ASC
            );

        if ($rootNode instanceof Node) {
            $queryBuilder
                ->andWhere('node.lft >= :left')
                ->setParameter(
                    'left',
                    $rootNode->getLeft()
                )
                ->andWhere('node.rgt <= :right')
                ->setParameter(
                    'right',
                    $rootNode->getRight()
                )
            ;
        }

        $this->applyFilterQueryBuilder(
            $queryBuilder,
            $abstractFilterDataObject
        );

        return $queryBuilder;
    }
}
