<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\EventSubscriber\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use Esites\KunstmaanExtrasBundle\ValueObject\Collections\NodeTranslationInterfaceCollection;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;

class PageSubscriber implements EventSubscriber
{
    private NodeTranslationInterfaceCollection $entitiesWithoutNodeTranslation;

    public function __construct()
    {
        $this->entitiesWithoutNodeTranslation = new NodeTranslationInterfaceCollection();
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof NodeTranslationInterface) {
            return;
        }

        $this->entitiesWithoutNodeTranslation->addElement($entity);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!$this->entitiesWithoutNodeTranslation->count()) {
            return;
        }

        $entityManager = $args->getEntityManager();
        $hasPersistedEntities = false;

        /** @var NodeTranslationInterface $entity */
        foreach ($this->entitiesWithoutNodeTranslation as $entity) {
            $isProcessed = $this->processEntity(
                $entityManager,
                $entity
            );

            if ($isProcessed) {
                $hasPersistedEntities = true;
            }
        }

        if ($hasPersistedEntities > 0) {
            $entityManager->flush();
        }
    }

    private function processEntity(
        EntityManagerInterface $entityManager,
        NodeTranslationInterface $entity
    ): bool {
        if (!$entity instanceof HasNodeInterface) {
            $this->entitiesWithoutNodeTranslation->removeElement($entity);

            return false;
        }

        /** @var NodeTranslationRepository $nodeTranslationRepository */
        $nodeTranslationRepository = $entityManager->getRepository(NodeTranslation::class);
        $nodeTranslation = $nodeTranslationRepository->getNodeTranslationFor($entity);

        if (!$nodeTranslation instanceof NodeTranslation) {
            return false;
        }

        $entity->setNodeTranslation($nodeTranslation);
        $entityManager->persist($entity);
        $this->entitiesWithoutNodeTranslation->removeElement($entity);

        return true;
    }
}
