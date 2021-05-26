<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Traits;

use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Esites\KunstmaanExtrasBundle\ValueObject\NodeJoinParametersValueObject;
use Locale;

trait NodeInnerJoinTrait
{
    private int $uniqueJoinIncrementCounter = 0;

    public function innerJoinEntityToPage(
        QueryBuilder $queryBuilder,
        NodeJoinParametersValueObject $nodeJoinParametersValueObject
    ): QueryBuilder {
        $counter = ++$this->uniqueJoinIncrementCounter;

        $nodeTableAlias = 'nodes_' . $counter;
        $nodeVersion = 'nodeVersion_' . $counter;
        $nodeTranslation = 'nodeTranslation' . $counter;

        $queryBuilder->innerJoin(
            $nodeJoinParametersValueObject->getEntityAlias()
            . '.' .
            $nodeJoinParametersValueObject->getFieldNameWithNodeRelation(),
            $nodeJoinParametersValueObject->getPageAlias()
        );

        $queryBuilder->innerJoin(
            'KunstmaanNodeBundle:NodeVersion',
            $nodeVersion,
            Join::WITH,
            $nodeJoinParametersValueObject->getPageAlias() . '.id = ' . $nodeVersion . '.refId'
        );

        $queryBuilder->innerJoin(
            'KunstmaanNodeBundle:NodeTranslation',
            $nodeTranslation,
            Join::WITH,
            $nodeTranslation . '.publicNodeVersion = ' . $nodeVersion . '.id'
        );

        $queryBuilder->innerJoin(
            'KunstmaanNodeBundle:Node',
            $nodeTableAlias,
            Join::WITH,
            $nodeTranslation . '.node = ' . $nodeTableAlias . '.id'
        );

        $queryBuilder
            ->andWhere($nodeTableAlias . '.deleted = 0')
            ->andWhere($nodeTranslation . '.online = 1')
            ->andWhere($nodeTranslation . '.lang = :lang')
            ->andWhere($nodeVersion . '.refEntityName = :entityRefName')
            ->setParameter('lang', Locale::getDefault())
            ->setParameter('entityRefName', $nodeJoinParametersValueObject->getPageEntityReference())
        ;

        return $queryBuilder;
    }

    public function innerJoinNodePageToPage(
        QueryBuilder $queryBuilder,
        NodeJoinParametersValueObject $nodeJoinParametersValueObject
    ): QueryBuilder {
        $counter = ++$this->uniqueJoinIncrementCounter;

        $nodeTableAlias = 'nodes_'.$counter;
        $nodeVersion = 'nodeVersion_'.$counter;
        $nodeTranslation = 'nodeTranslation'.$counter;

        $queryBuilder->innerJoin(
            'page.'.$nodeJoinParametersValueObject->getFieldNameWithNodeRelation(),
            $nodeTableAlias
        );

        $queryBuilder->innerJoin($nodeTableAlias.'.nodeTranslations', $nodeTranslation);

        $queryBuilder->innerJoin(
            'KunstmaanNodeBundle:NodeVersion',
            $nodeVersion,
            Join::WITH,
            $nodeTranslation.'.publicNodeVersion = '.$nodeVersion.'.id'
        );

        $queryBuilder->innerJoin(
            $nodeJoinParametersValueObject->getPageEntityReference(),
            $nodeJoinParametersValueObject->getPageAlias(),
            Join::WITH,
            $nodeJoinParametersValueObject->getPageAlias().'.id = '.$nodeVersion.'.refId'
        );

        $queryBuilder
            ->andWhere($nodeTableAlias.'.deleted = 0')
            ->andWhere($nodeTranslation.'.online = 1')
            ->andWhere($nodeTranslation.'.lang = :lang')
            ->setParameter('lang', \Locale::getDefault());

        return $queryBuilder;
    }
}
