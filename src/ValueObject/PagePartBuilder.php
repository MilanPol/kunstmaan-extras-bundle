<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject;

class PagePartBuilder
{
    private string $region;

    /**
     * @var callable
     */
    private $callable;


    public function __construct(
        string $region,
        callable $callable
    ) {
        $this->region = $region;
        $this->callable = $callable;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }
}