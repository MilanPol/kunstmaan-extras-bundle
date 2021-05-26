<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TrailingSlashRedirectSubscriber implements EventSubscriberInterface
{
    private bool $enableTrailingSlashRedirect;

    public function __construct(bool $enableTrailingSlashRedirect)
    {
        $this->enableTrailingSlashRedirect = $enableTrailingSlashRedirect;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        if (!$this->enableTrailingSlashRedirect) {
            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$event->getThrowable() instanceof NotFoundHttpException) {
            return;
        }

        $request = $event->getRequest();
        $pathInfo = $request->getPathInfo();

        if (!$this->endsWithSlash($pathInfo)) {
            return;
        }

        $url = $this->buildNewUrl(
            $request,
            $pathInfo
        );

        $event->setResponse(
            new RedirectResponse($url)
        );
    }

    private function endsWithSlash(string $path): bool
    {
        $lastCharacterPosition = strlen($path) - 1;

        return $path[$lastCharacterPosition] === '/';
    }

    private function buildNewUrl(
        Request $request,
        string $pathInfo
    ): string {
        $queryString = $request->getQueryString();

        if (is_string($queryString)) {
            $queryString = '?' . $queryString;
        }

        $newUri = substr(
            $pathInfo,
            0,
            -1
        );

        return $request->getSchemeAndHttpHost() . $newUri . $queryString;
    }
}
