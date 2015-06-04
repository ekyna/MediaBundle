<?php

namespace Ekyna\Bundle\MediaBundle\Uploader;

use Ekyna\Bundle\MediaBundle\Model\UploadableInterface;
use Gaufrette\Filesystem;

/**
 * Class Uploader
 * @package Ekyna\Bundle\MediaBundle\Uploader
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Uploader implements UploaderInterface
{
    /**
     * @var string
     */
    private $uploadDirectory;

    /**
     * @var Filesystem
     */
    private $targetFilesystem;


    /**
     * Constructor.
     *
     * @param string $uploadDirectory
     */
    public function __construct($uploadDirectory)
    {
        $this->uploadDirectory = rtrim($uploadDirectory, '/') . '/';
    }

    /**
     * Sets the target filesystem.
     *
     * @param Filesystem $filesystem
     * @return Uploader
     */
    public function setTargetFilesystem(Filesystem $filesystem)
    {
        $this->targetFilesystem = $filesystem;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(UploadableInterface $uploadable)
    {
        if ($uploadable->hasFile() || $uploadable->shouldBeRenamed()) {
            $uploadable->setOldPath($uploadable->getPath());
            $this->generatePath($uploadable);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadableInterface $uploadable)
    {
        if ($uploadable->hasPath()) {
            if ($uploadable->hasFile()) {
                $this->targetFilesystem->write(
                    $uploadable->getPath(),
                    file_get_contents($uploadable->getFile()->getPathname())
                );
                $uploadable->setFile(null);
            } elseif ($uploadable->hasOldPath()) {
                $this->targetFilesystem->rename($uploadable->getOldPath(), $uploadable->getPath());
            }
        }

        $this->remove($uploadable);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(UploadableInterface $uploadable)
    {
        if ($uploadable->hasKey()) {
            $path = $this->uploadDirectory . $uploadable->getKey();
            if (file_exists($path)) {
                @unlink($path);
            }
            $uploadable->setKey(null);
        }

        if ($uploadable->hasOldPath()) {
            $oldPath = $uploadable->getOldPath();
            if ($this->targetFilesystem->has($oldPath)) {
                $this->targetFilesystem->delete($oldPath);
            }
            $uploadable->setOldPath(null);
        }
    }

    /**
     * Generates a unique path.
     * 
     * @param UploadableInterface $uploadable
     */
    private function generatePath(UploadableInterface $uploadable)
    {
        $filename = $uploadable->guessFilename();

        do {
            $hash = md5(uniqid(mt_rand()));
            $path = sprintf(
                '%s/%s/%s',
                substr($hash, 0, 3),
                substr($hash, 3, 3),
                $filename
            );
        } while ($this->targetFilesystem->has($path));

        $uploadable->setPath($path);
    }
}
