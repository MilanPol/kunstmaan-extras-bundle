<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Helper;

class PageCreatorConfig implements PageCreatorConfigInterface
{
	protected const ADMIN_USERNAME = 'admin';

    protected array $titles = [];

    protected array $slugs = [];

    protected bool $hiddenFromNav = false;

    protected bool $online = true;

    protected ?string $creator = self::ADMIN_USERNAME;

    protected ?object $creatorObject = null;

    protected ?string $internalName = null;

    protected int $weight = 0;

	public function getTitle(string $locale = '_default'): string
	{
		if (isset($this->titles[$locale])) {
			return $this->titles[$locale];
		}

		return $this->titles['_default'] ?? '';
	}

	public function setTitle(string $title = null, string $locale = '_default'): PageCreatorConfigInterface
	{
		if ($title !== null) {
			$this->titles[$locale] = $title;
		}

		return $this;
	}

	public function setTitles(array $titles): PageCreatorConfigInterface
	{
		if ($titles !== null) {
			$this->titles = $titles;
		}

		return $this;
	}

	public function getSlug(string $locale = '_default'): string
	{
		if (isset($this->slugs[$locale])) {
			return $this->slugs[$locale];
		}

		return
			$this->slugs['_default'] ??
			strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->getTitle($locale))));
	}

	public function setSlug(string $slug = null, string $locale = '_default'): PageCreatorConfigInterface
	{
		if ($slug !== null) {
			$this->slugs[$locale] = $slug;
		}

		return $this;
	}

	public function setSlugs(array $slugs): PageCreatorConfigInterface
	{
		$this->slugs = $slugs;

		return $this;
	}

	public function getHiddenFromNav(): bool
	{
		return $this->hiddenFromNav;
	}

	public function setHiddenFromNav(bool $hiddenFromNav): PageCreatorConfigInterface
	{
		$this->hiddenFromNav = $hiddenFromNav;

		return $this;
	}

	public function getOnline(): bool
	{
		return $this->online;
	}

	public function setOnline(bool $online): PageCreatorConfigInterface
	{
		$this->online = $online;

		return $this;
	}

	public function getCreator(): ?string
	{
		return $this->creator;
	}

	public function setCreator(?string $creator = null): PageCreatorConfigInterface
	{
		$this->creator = $creator;

		return $this;
	}

	public function getInternalName(): ?string
	{
		return $this->internalName;
	}

	public function setInternalName(?string $internalName = null): PageCreatorConfigInterface
	{
		$this->internalName = $internalName;

		return $this;
	}

	public function getWeight(): int
	{
		return $this->weight ?? 0;
	}

	public function setWeight(int $weight): PageCreatorConfigInterface
	{
		$this->weight = $weight;

		return $this;
	}

    public function getCreatorObject(): ?object
    {
        return $this->creatorObject;
    }

    public function setCreatorObject(object $creator = null): PageCreatorConfigInterface
    {
        $this->creatorObject = $creator;

        return $this;
    }
}
