<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Fixtures;

class RegionFixtureObject
{
    private string $regionName;

    private int $regionNumber;

    public function __construct(string $regionName, int $regionNumber)
    {
        $this->regionName = $regionName;
        $this->regionNumber = $regionNumber;
    }

    public function getRegionName(): string
    {
        return $this->regionName;
    }

    public function getRegionNumber(): int
    {
        return $this->regionNumber;
    }
}
