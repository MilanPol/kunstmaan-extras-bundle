<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Esites\KunstmaanExtrasBundle\Constants\PaginationConstants;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PagerfantaService
{
    private ?Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    public function getPagerfanta(
        QueryBuilder $queryBuilder,
        int $itemsPerPage = PaginationConstants::DEFAULT_ITEMS_PER_PAGE,
        bool $fetchJoinCollection = true
    ): Pagerfanta {
        $adapter = new DoctrineORMAdapter($queryBuilder, $fetchJoinCollection);

        $currentPage = 1;

        if ($this->request instanceof Request) {
            $currentPage = $this->request->get(PaginationConstants::QUERY_PARAMETER, $currentPage);
        }

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($itemsPerPage);
        $pagerfanta->setCurrentPage($currentPage);
        
        return $pagerfanta;
    }
}
