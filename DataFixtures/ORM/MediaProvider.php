<?php

namespace Ekyna\Bundle\MediaBundle\DataFixtures\ORM;

use Ekyna\Bundle\CoreBundle\DataFixtures\ORM\Fixtures;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\FolderRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class MediaProvider
 * @package Ekyna\Bundle\MediaBundle\DataFixtures\ORM
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaProvider
{
    /**
     * @var FolderRepositoryInterface
     */
    private $folderRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;


    /**
     * Constructor.
     *
     * @param FolderRepositoryInterface $folderRepository
     * @param MediaRepository           $mediaRepository
     */
    public function __construct(FolderRepositoryInterface $folderRepository, MediaRepository $mediaRepository)
    {
        $this->folderRepository = $folderRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Returns the root media folder.
     *
     * @return FolderInterface
     */
    public function rootMediaFolder(): FolderInterface
    {
        return $this->folderRepository->findRoot();
    }

    /**
     * Returns a random media image.
     *
     * @return MediaInterface
     */
    public function randomImage(): MediaInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->mediaRepository->findRandomOneBy(['type' => MediaTypes::IMAGE]);
    }

    /**
     * Fake upload the media file.
     *
     * @param string $path
     * @param int    $num
     *
     * @return UploadedFile
     */
    public function uploadMedia(string $path, int $num = null): UploadedFile
    {
        if (null !== $num) {
            $path = sprintf($path, $num);
        }

        return Fixtures::uploadFile(__DIR__ . '/../../Resources/fixtures/' . $path);
    }

    /**
     * Returns the media fixtures root directory.
     *
     * @return string
     */
    public function mediaFixturesRoot(): string
    {
        return __DIR__ . '/../../Resources/fixtures';
    }
}
