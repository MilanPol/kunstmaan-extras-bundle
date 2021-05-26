<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject;

class NodeJoinParametersValueObject
{
    private string $fieldNameWithNodeRelation;

    private string $pageAlias;

    private string $pageEntityReference;

    private string $entityAlias;

    public function __construct(
        string $fieldNameWithNodeRelation,
        string $pageAlias,
        string $pageEntityReference,
        string $entityAlias = ''
    ) {
        $this->fieldNameWithNodeRelation = $fieldNameWithNodeRelation;
        $this->pageAlias = $pageAlias;
        $this->pageEntityReference = $pageEntityReference;
        $this->entityAlias = $entityAlias;
    }

    public function getFieldNameWithNodeRelation(): string
    {
        return $this->fieldNameWithNodeRelation;
    }

    public function getPageAlias(): string
    {
        return $this->pageAlias;
    }

    public function getPageEntityReference(): string
    {
        return $this->pageEntityReference;
    }

    public function getEntityAlias(): string
    {
        return $this->entityAlias;
    }
}
