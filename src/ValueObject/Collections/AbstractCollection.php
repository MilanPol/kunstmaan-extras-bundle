<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\ValueObject\Collections;

use ArrayIterator;
use Assert\Assertion;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;

/**
 * @deprecated use Esites\SymfonyExtrasBundle\ValueObject\Collections\AbstractCollection instead
 */
abstract class AbstractCollection implements IteratorAggregate
{
    /**
     * @return class-string
     */
    abstract public function getClassOfElement(): string;

    protected ArrayCollection $collection;

    public function __construct(array $elements = [])
    {
        $this->collection = new ArrayCollection();

        $this->addElements($elements);
    }

    public function addElement(object $element): self
    {
        Assertion::isInstanceOf(
            $element,
            $this->getClassOfElement()
        );

        $this->collection->add($element);

        return $this;
    }

    public function addElements(array $elements): self
    {
        Assertion::allIsInstanceOf(
            $elements,
            $this->getClassOfElement()
        );
        foreach ($elements as $element) {
            $this->addElement($element);
        }

        return $this;
    }

    public function removeElement(object $element): self
    {
        Assertion::isInstanceOf(
            $element,
            $this->getClassOfElement()
        );

        $this->collection->removeElement($element);

        return $this;
    }

    public function clear(): self
    {
        $this->collection->clear();

        return $this;
    }

    public function toArray(): array
    {
        $list = $this->collection->toArray();
        Assertion::allIsInstanceOf(
            $list,
            $this->getClassOfElement()
        );

        return $list;
    }

    public function contains(object $element): bool
    {
        Assertion::isInstanceOf(
            $element,
            $this->getClassOfElement()
        );

        return $this->collection->contains($element);
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    public function first(): object
    {
        Assertion::greaterThan(
            $this->count(),
            0
        );

        return $this->collection[0];
    }
}
