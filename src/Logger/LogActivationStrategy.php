<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Logger;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @deprecated
 */
class LogActivationStrategy extends ErrorLevelActivationStrategy
{
    public function __construct()
    {
        parent::__construct('error');
    }

    public function isHandlerActivated(array $record): bool
    {
        $isActivated = parent::isHandlerActivated($record);

        if (!$isActivated || !isset($record['context']['exception'])) {
            return $isActivated;
        }

        $exception = $record['context']['exception'];

        if ($exception instanceof HttpException) {
            return $exception->getStatusCode() >= 500;
        }

        return $isActivated;
    }
}
