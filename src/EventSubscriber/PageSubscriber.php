<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PageSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::ADD_NODE => 'addNode',
        ];
    }

    public function addNode(NodeEvent $event): void
    {
        $page = $event->getPage();

        if (!$page instanceof NodeTranslationInterface) {
            return;
        }

        $page->setNodeTranslation(
            $event->getNodeTranslation()
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();
    }
}
