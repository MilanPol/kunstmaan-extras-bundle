<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ConfigBundle\Entity\AbstractConfig;

/**
 * @ORM\MappedSuperclass()
 */
abstract class AbstractDefaultSiteConfig extends AbstractConfig
{
    abstract public function getDefaultAdminType(): string;

    abstract public function getLanguagePrefix(): string;

    public function getLabel(): string
    {
        return 'Configuration '.static::getLanguagePrefix();
    }

    abstract public function getInternalName(): string;

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }
}
