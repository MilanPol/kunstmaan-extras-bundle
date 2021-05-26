<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataFixtures\ORM;

use Kunstmaan\MediaBundle\Entity\Folder;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

abstract class AbstractMediaFixture extends AbstractFixture
{
    private ?string $baseDir = null;

    private ?Folder $baseFolder = null;

    public function load(ObjectManager $objectManager): void
    {
        $folderRepository = $objectManager->getRepository(Folder::class);

        $baseFolder = $folderRepository->findOneBy(
            [
                'rel' => Folder::TYPE_IMAGE,
            ]
        );

        if (!$baseFolder instanceof Folder) {
            return;
        }

        $this->baseDir = basename($this->getDummyFolder());
        $this->baseFolder = $baseFolder;

        $finder = new Finder();
        $finder->files()->in($this->getDummyFolder());

        foreach ($finder as $file) {
            $this->createFile(
                $file,
                $objectManager
            );
        }

        $objectManager->flush();
    }

    private function createFile(
        SplFileInfo $file,
        ObjectManager $objectManager
    ): void {
        $path = (string) $file->getRealPath();
        $dirName = basename(dirname($path));
        $folder = null;

        if ($dirName === $this->baseDir) {
            $folder = $this->baseFolder;
        }

        $folder = $this->getOrCreateFolder(
            $objectManager,
            $folder,
            $dirName
        );

        $media = $this->mediaCreator->createFile(
            $path,
            $folder->getId()
        );

        $objectManager->persist($media);
    }

    private function getOrCreateFolder(
        ObjectManager $objectManager,
        ?Folder $folder,
        string $dirName
    ): Folder {
        if ($folder instanceof Folder) {
            return $folder;
        }

        $folder = $objectManager
            ->getRepository(Folder::class)
            ->findOneBy(
                [
                    'internalName' => $dirName,
                ]
            )
        ;

        if ($folder instanceof Folder) {
            return $folder;
        }

        $folder = new Folder();
        $folder->setInternalName($dirName);
        $folder->setParent($this->baseFolder);
        $folder->setName($dirName);

        $objectManager->persist($folder);
        $objectManager->flush();

        return $folder;
    }

    abstract public function getDummyFolder(): string;

    abstract public function getOrder(): int;
}
