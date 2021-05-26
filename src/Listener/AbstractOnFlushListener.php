<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;

abstract class AbstractOnFlushListener
{
    protected EntityManagerInterface $entityManager;

    protected UnitOfWork $unitOfWork;

    public function onFlush(OnFlushEventArgs $args): void
    {
        $this->entityManager = $args->getEntityManager();
        $this->unitOfWork = $this->entityManager->getUnitOfWork();

        $entities = $this->unitOfWork->getScheduledEntityUpdates();

        foreach ($entities as $entity) {
            $this->handleEntityUpdate($entity);
        }

        $entities = $this->unitOfWork->getScheduledEntityInsertions();

        foreach ($entities as $entity) {
            $this->handleEntityInsertion($entity);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function handleEntityUpdate(object $entity): void
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function handleEntityInsertion(object $entity): void
    {
    }

    protected function addEntityToChangeSet(object $entity): void
    {
        $classMetadata = $this->entityManager->getClassMetadata(get_class($entity));
        $this->unitOfWork->computeChangeSet($classMetadata, $entity);
    }

    protected function updateEntityInChangeSet(object $entity): void
    {
        $classMetadata = $this->entityManager->getClassMetadata(get_class($entity));
        $this->unitOfWork->recomputeSingleEntityChangeSet($classMetadata, $entity);
    }
}
