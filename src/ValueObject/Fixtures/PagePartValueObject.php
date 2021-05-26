<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Fixtures;

class PagePartValueObject
{
    private string $class;

    /**
     * @var callable
     */
    private $callback;

    public function __construct(string $class, callable $callback)
    {
        $this->class    = $class;
        $this->callback = $callback;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }
}
