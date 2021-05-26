<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class AbstractLanguageSubscriber implements EventSubscriberInterface
{
    abstract public function getAvailableLanguages(): array;

    abstract public function getDefaultLanguage(): string;

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        $exception = $exceptionEvent->getThrowable();

        if (!$exception instanceof NotFoundHttpException) {
            return;
        }

        $request = $exceptionEvent->getRequest();
        $uri = $request->getRequestUri();

        if (preg_match('~/[a-z]{2}/~', $uri)) {
            return;
        }

        $language = $this->getLanguage($request);

        $exceptionEvent->setResponse(
            new RedirectResponse(
                '/'.$language.$uri
            )
        );
    }

    private function getLanguage(Request $request): string
    {
        $languages = $this->getAvailableLanguages();

        foreach ($request->getLanguages() as $language) {
            if (isset($languages[$language])) {
                return $language;
            }
        }

        return $this->getDefaultLanguage();
    }
}
