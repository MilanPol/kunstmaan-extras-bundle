<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Esites\KunstmaanExtrasBundle\Interfaces\NodeTranslationInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetNodeTranslationCommand extends Command
{
    public const NAME = 'esites:save:node-translation';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setName(static::NAME)
            ->setDescription('Save node translation to all entities implementing NodeTranslationInterface')
        ;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): ?int {
        $classMetaDatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($classMetaDatas as $classMetaData) {
            $this->processClassMetaData(
                $output,
                $classMetaData
            );
        }

        return null;
    }

    private function processClassMetaData(
        OutputInterface $output,
        ClassMetadata $classMetadata
    ): void {
        $reflectionClass = $classMetadata->getReflectionClass();

        if ($reflectionClass->isAbstract()) {
            return;
        }

        $hasNodeInterface = $reflectionClass->implementsInterface(NodeTranslationInterface::class);

        if (!$hasNodeInterface) {
            return;
        }

        $hasNodeTranslationInterface = $reflectionClass->implementsInterface(NodeTranslationInterface::class);

        if (!$hasNodeTranslationInterface) {
            return;
        }

        $this->setNodeTranslation($classMetadata);
        $output->writeln('Updated node translations for ' . $classMetadata->getName());
    }

    private function setNodeTranslation(ClassMetadata $classMetadata): void
    {
        /** @var class-string $classString */
        $classString = $classMetadata->getName();

        /** @var NodeTranslationRepository $nodeTranslationRepository */
        $nodeTranslationRepository = $this->entityManager->getRepository(NodeTranslation::class);

        $entities = $this->entityManager->getRepository($classString)->findAll();

        foreach ($entities as $entity) {
            if (!$entity instanceof HasNodeInterface || !$entity instanceof NodeTranslationInterface) {
                continue;
            }

            $nodeTranslation = $nodeTranslationRepository->getNodeTranslationFor($entity);
            $entity->setNodeTranslation($nodeTranslation);

            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }
}
