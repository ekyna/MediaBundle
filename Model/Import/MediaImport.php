<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model\Import;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use RuntimeException;

use function in_array;

/**
 * Class MediaImport
 * @package Ekyna\Bundle\MediaBundle\Model\Import
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImport
{
    protected FolderInterface $folder;
    protected string          $filesystem = 'local_ftp';
    /** @var string[] */
    protected array $keys = [];
    /** @var array|MediaInterface[] */
    protected array $medias = [];


    /**
     * Constructor.
     *
     * @param FolderInterface $folder
     */
    public function __construct(FolderInterface $folder)
    {
        $this->folder = $folder;
    }

    /**
     * Returns the folder.
     *
     * @return FolderInterface
     */
    public function getFolder(): FolderInterface
    {
        return $this->folder;
    }

    /**
     * Sets the filesystem.
     *
     * @param string $filesystem
     *
     * @return MediaImport
     */
    public function setFilesystem(string $filesystem): MediaImport
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Returns the filesystem.
     *
     * @return string
     */
    public function getFilesystem(): string
    {
        return $this->filesystem;
    }

    /**
     * Sets the keys.
     *
     * @param array $keys
     *
     * @return MediaImport
     */
    public function setKeys(array $keys): MediaImport
    {
        $this->keys = $keys;

        return $this;
    }

    /**
     * Returns the keys.
     *
     * @return array
     */
    public function getKeys(): array
    {
        return $this->keys;
    }

    /**
     * Adds the media.
     *
     * @param MediaInterface $media
     *
     * @return MediaImport
     */
    public function addMedia(MediaInterface $media): MediaImport
    {
        if (!in_array($media->getKey(), $this->keys)) {
            throw new RuntimeException("Key {$media->getKey()} is not selected.");
        }

        foreach ($this->medias as $selected) {
            if ($selected->getKey() == $media->getKey()) {
                return $this;
            }
        }

        $media->setFolder($this->folder);

        $this->medias[] = $media;

        return $this;
    }

    /**
     * Returns the medias.
     *
     * @return array|MediaInterface[]
     */
    public function getMedias(): array
    {
        return $this->medias;
    }
}
