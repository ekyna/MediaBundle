<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\DataFixtures\ORM;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\FolderRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepository;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function copy;
use function is_file;
use function pathinfo;
use function sprintf;
use function sys_get_temp_dir;
use function uniqid;

use const PATHINFO_BASENAME;
use const PATHINFO_EXTENSION;

/**
 * Class MediaProvider
 * @package Ekyna\Bundle\MediaBundle\DataFixtures\ORM
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaProvider
{
    private FolderRepositoryInterface $folderRepository;
    private MediaRepository           $mediaRepository;

    public function __construct(
        FolderRepositoryInterface $folderRepository,
        MediaRepository $mediaRepository
    ) {
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
     * @param string   $path
     * @param int|null $num
     *
     * @return UploadedFile
     */
    public function uploadMedia(string $path, int $num = null): UploadedFile
    {
        if (null !== $num) {
            $path = sprintf($path, $num);
        }

        $path = __DIR__ . "/../../Resources/fixtures/media/$path";

        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('Source file %s not found.', $path));
        }

        $fileName = pathinfo($path, PATHINFO_BASENAME);
        $target = sys_get_temp_dir() . '/' . uniqid() . '.' . pathinfo($path, PATHINFO_EXTENSION);

        if (!copy($path, $target)) {
            throw new RuntimeException(sprintf('Failed to copy %s file.', $fileName));
        }

        return new UploadedFile($target, $fileName, null, null, true); // Last arg fakes the upload test
    }
}
