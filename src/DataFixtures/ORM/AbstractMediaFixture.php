<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataFixtures\ORM;

use Kunstmaan\MediaBundle\Entity\Folder;
use Doctrine\Common\Persistence\ObjectManager;
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
            $this->createFile($file, $objectManager);
        }

        $objectManager->flush();
    }

    private function createFile(SplFileInfo $file, ObjectManager $objectManager): void
    {
        $path = (string)$file->getRealPath();
        $dirName = basename(dirname($path));

        $folder = null;

        if ($dirName === $this->baseDir) {
            $folder = $this->baseFolder;
        }

        if (!$folder instanceof Folder) {
            $folder = $objectManager
                ->getRepository(Folder::class)
                ->findOneBy(
                    [
                        'internalName' => $dirName,
                    ]
                )
            ;
        }

        if (!$folder instanceof Folder) {
            $folder = new Folder();
            $folder->setInternalName($dirName);
            $folder->setParent($this->baseFolder);
            $folder->setName($dirName);

            $objectManager->persist($folder);
            $objectManager->flush();
        }

        $media = $this->mediaCreator->createFile($file, $folder->getId());

        $objectManager->persist($media);
    }

    abstract public function getDummyFolder(): string;

    abstract public function getOrder(): int;
}
