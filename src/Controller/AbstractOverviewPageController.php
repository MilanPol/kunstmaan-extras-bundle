<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Esites\KunstmaanExtrasBundle\DataObject\AbstractFilterDataObject;
use Esites\KunstmaanExtrasBundle\Repository\AbstractFilteredPageRepository;
use Esites\KunstmaanExtrasBundle\Service\AjaxService;
use Esites\KunstmaanExtrasBundle\Service\PagerfantaService;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractOverviewPageController extends AbstractController
{
    public const NUMBER_OF_OVERVIEW_ITEMS = 10;

    protected EntityManagerInterface $entityManager;

    protected PagerfantaService $pagerfantaService;

    protected AjaxService $ajaxService;

    protected RequestStack $requestStack;

    protected DomainConfigurationInterface $domainConfiguration;

    public function __construct(
        DomainConfigurationInterface $domainConfiguration,
        EntityManagerInterface $entityManager,
        PagerfantaService $pagerfantaService,
        AjaxService $ajaxService,
        RequestStack $requestStack
    ) {
        $this->entityManager = $entityManager;
        $this->pagerfantaService = $pagerfantaService;
        $this->ajaxService = $ajaxService;
        $this->requestStack = $requestStack;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * @return class-string
     */
    abstract public function getPageClass(): string;

    /**
     * return $this->getServiceResponse(); when defining this method
     */
    abstract public function serviceAction(): array;

    /**
     * return $this->getAjaxResponse(); when defining this method
     * also add a route to the method
     */
    abstract public function ajaxAction(): JsonResponse;

    abstract public function getAjaxTemplate(): string;

    public function getFilterClass(): ?string
    {
        return null;
    }

    /**
     * Overwrite if you want to change the number of items rendered
     */
    public function getNumberOfOverviewItems(): int
    {
        return static::NUMBER_OF_OVERVIEW_ITEMS;
    }

    /**
     * Overwrite method if you want to pass more parameters to the template
     */
    public function getParameters(): array
    {
        return [];
    }

    protected function getPagerfanta(Request $request): Pagerfanta
    {
        $abstractFilteredPageRepository = $this->entityManager->getRepository($this->getPageClass());

        if (!$abstractFilteredPageRepository instanceof AbstractFilteredPageRepository) {
            throw new InvalidTypeException(
                sprintf(
                    'The repository of your page %s has to extend %s',
                    $this->getPageClass(),
                    AbstractFilteredPageRepository::class
                )
            );
        }

        $queryBuilder = $abstractFilteredPageRepository->getPagesByOverviewPageQueryBuilder(
            $this->domainConfiguration->getRootNode(),
            $this->applyFilter($request)
        );

        return $this->pagerfantaService->getPagerfanta(
            $queryBuilder,
            $this->getNumberOfOverviewItems()
        );
    }

    protected function getFilterForm(Request $request): ?FormInterface
    {
        $filterClass = $this->getFilterClass();

        if (!is_string($filterClass)) {
            return null;
        }

        $form = $this->createForm($filterClass);
        $form->handleRequest($request);

        return $form;
    }

    protected function applyFilter(Request $request): ?AbstractFilterDataObject
    {
        $form = $this->getFilterForm($request);

        if (!$form instanceof FormInterface) {
            return null;
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return null;
        }

        $data = $form->getData();

        if (!$data instanceof AbstractFilterDataObject) {
            return null;
        }

        return $data;
    }

    protected function getRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw $this->createAccessDeniedException(
                'Invalid request'
            );
        }

        return $request;
    }

    protected function getAjaxResponse(): JsonResponse
    {
        $request = $this->getRequest();

        return $this->ajaxService->getAjaxResponse(
            $this->getPagerfanta($request),
            $this->getAjaxTemplate(),
            $this->getTemplateParameters()
        );
    }

    protected function getTemplateParameters(): array
    {
        $parameters = $this->getParameters();
        $request = $this->getRequest();
        $pagerfanta = $this->getPagerfanta($request);
        $filterForm = $this->getFilterForm($request);

        $parameters['pagerfanta'] = $pagerfanta;
        $parameters['numberOfItemsOnNextPage'] = $this->ajaxService->getNumberOfItemsOnNextPage($pagerfanta);

        if ($filterForm instanceof FormInterface) {
            $parameters['form'] = $filterForm->createView();
        }

        return $parameters;
    }

    protected function getServiceResponse(): array
    {
        return $this->getTemplateParameters();
    }
}
