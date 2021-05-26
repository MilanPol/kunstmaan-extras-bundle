<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Helper;

use Esites\KunstmaanExtrasBundle\Constants\ApplicationStateConstants;

class ApplicationStateHelper
{
    private string $state = ApplicationStateConstants::APPLICATION_STATE_NORMAL;

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): ApplicationStateHelper
    {
        $this->state = $state;

        return $this;
    }

    public function hasState(string $state): bool
    {
        return $this->getState() === $state;
    }
}
