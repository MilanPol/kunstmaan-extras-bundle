<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Helper;

interface PageCreatorConfigInterface
{
    public function getTitle(string $locale = '_default'): ?string;

    public function setTitle(
        ?string $title = null,
        string $locale = '_default'
    ): PageCreatorConfigInterface;

    public function setTitles(array $titles): PageCreatorConfigInterface;

    public function getSlug(string $locale = '_default'): string;

    public function setSlug(
        ?string $slug = null,
        string $locale = '_default'
    ): PageCreatorConfigInterface;

    public function setSlugs(array $slugs): PageCreatorConfigInterface;

    public function getWeight(): int;

    public function setWeight(int $weight): PageCreatorConfigInterface;

    public function getInternalName(): ?string;

    public function setInternalName(?string $internalName = null): PageCreatorConfigInterface;

    public function getOnline(): bool;

    public function setOnline(bool $online): PageCreatorConfigInterface;

    public function getHiddenFromNav(): bool;

    public function setHiddenFromNav(bool $hiddenFromNav): PageCreatorConfigInterface;

    public function getCreator(): ?string;

    public function setCreator(?string $creator = null): PageCreatorConfigInterface;

    public function getCreatorObject(): ?object;

    public function setCreatorObject(?object $creator = null): PageCreatorConfigInterface;
}
