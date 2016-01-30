<?php

namespace Ekyna\Bundle\MediaBundle\Service;

use Doctrine\ORM\EntityManager;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class Browser
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Browser
{
    /**
     * @var FolderInterface
     */
    private $folder;

    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var MediaRepository
     */
    private $repository;

    /**
     * @var
     */
    private $thumbGenerator;

    /**
     * Constructor.
     *
     * @param EntityManager $manager
     * @param MediaRepository $repository
     * @param Generator $thumbGenerator
     */
    public function __construct(
        EntityManager $manager,
        MediaRepository $repository,
        Generator $thumbGenerator
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->thumbGenerator = $thumbGenerator;
    }

    /**
     * Sets the current folder.
     *
     * @param FolderInterface $folder
     * @return Browser
     */
    public function setFolder(FolderInterface $folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Returns the media found in the current folder.
     *
     * @param array $types
     * @return MediaInterface[]
     */
    public function findMedias(array $types = [])
    {
        if (null === $this->folder) {
            throw new \RuntimeException('No folder selected.');
        }

        $criteria = ['folder' => $this->folder];
        if (count($types)) {
            $criteria['type'] = $types;
        }

        /** @var MediaInterface[] $medias */
        $medias = $this->repository->findBy($criteria);

        foreach ($medias as $media) {
            $media
                ->setThumb($this->thumbGenerator->generateThumbUrl($media))
                ->setFront($this->thumbGenerator->generateFrontUrl($media))
                ->setPlayer($this->thumbGenerator->generatePlayerUrl($media))
            ;
        }

        return $medias;
    }
}
