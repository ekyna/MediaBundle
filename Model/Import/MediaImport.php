<?php

namespace Ekyna\Bundle\MediaBundle\Model\Import;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaImport
 * @package Ekyna\Bundle\MediaBundle\Model\Import
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImport
{
    /**
     * @var FolderInterface
     */
    protected $folder;

    /**
     * @var string
     */
    protected $filesystem = 'local_ftp';

    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var array|MediaInterface[]
     */
    protected $medias = [];


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
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Sets the filesystem.
     *
     * @param string $filesystem
     * @return MediaImport
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Returns the filesystem.
     *
     * @return string
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Sets the keys.
     *
     * @param array $keys
     * @return MediaImport
     */
    public function setKeys(array $keys)
    {
        $this->keys = $keys;
        return $this;
    }

    /**
     * Returns the keys.
     *
     * @return array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * Adds the media.
     *
     * @param MediaInterface $media
     * @return MediaImport
     */
    public function addMedia(MediaInterface $media)
    {
        if (!in_array($media->getKey(), $this->keys)) {
            throw new \RuntimeException("Key {$media->getKey()} is not selected.");
        }

        foreach ($this->medias as $m) {
            if ($m->getKey() == $media->getKey()) {
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
     * @return array|\Ekyna\Bundle\MediaBundle\Model\MediaInterface[]
     */
    public function getMedias()
    {
        return $this->medias;
    }
}
