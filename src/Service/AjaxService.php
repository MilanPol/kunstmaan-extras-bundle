<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Service;

use Exception;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AjaxService
{
    private Environment $environment;

    public function __construct(
        Environment $environment
    ) {
        $this->environment = $environment;
    }

    public function handleException(Exception $exception): JsonResponse
    {
        return $this->handleError($exception->getMessage());
    }

    public function handleError(string $error): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => $error,
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    public function handleInvalidForm(FormInterface $form): JsonResponse
    {
        $formErrors = $form->getErrors(
            true,
            true
        );
        $errors = [];

        foreach ($formErrors as $formError) {
            if (!$formError instanceof FormError) {
                continue;
            }

            $errors[$this->getFieldId($formError->getOrigin())][] = $formError->getMessage();
        }

        return new JsonResponse(
            [
                'errors' => $errors,
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    private function getFieldId(
        ?FormInterface $form,
        array $fields = []
    ): string {
        if (!$form instanceof FormInterface) {
            return implode(
                '_',
                array_reverse($fields)
            );
        }

        $fields[] = $form->getName();

        return $this->getFieldId(
            $form->getParent(),
            $fields
        );
    }

    public function getAjaxResponse(
        Pagerfanta $pagerfanta,
        string $template,
        array $templateParameters
    ): JsonResponse {
        $html = $this->environment->render(
            $template,
            $templateParameters
        );

        return new JsonResponse(
            [
                'html'                    => $html,
                'currentPage'             => $pagerfanta->getCurrentPage(),
                'maximumNumberOfPages'    => $pagerfanta->getNbPages(),
                'hasNextPage'             => $pagerfanta->hasNextPage(),
                'numberOfResults'         => $pagerfanta->getNbResults(),
                'numberOfItemsOnNextPage' => $this->getNumberOfItemsOnNextPage($pagerfanta),
            ]
        );
    }

    public function getNumberOfItemsOnNextPage(Pagerfanta $pagerfanta): int
    {
        if (!$pagerfanta->hasNextPage()) {
            return 0;
        }

        if ($pagerfanta->getNextPage() < $pagerfanta->getNbPages()) {
            return $pagerfanta->getMaxPerPage();
        }

        $numberOfItemsOnNextPage = $pagerfanta->getNbResults() % $pagerfanta->getMaxPerPage();

        if ($numberOfItemsOnNextPage > 0) {
            return $numberOfItemsOnNextPage;
        }

        return $pagerfanta->getMaxPerPage();
    }
}
