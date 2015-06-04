<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model\TimestampableTrait;
use Ekyna\Bundle\MediaBundle\Model\UploadableInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AbstractUploadable
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractUploadable implements UploadableInterface
{
    use TimestampableTrait;

    /**
     * The key for the upload filesystem
     *
     * @var string
     */
    protected $key;

    /**
     * File uploaded
     *
     * @var File
     */
    protected $file;

    /**
     * Path
     *
     * @var string
     */
    protected $path;

    /**
     * Old path (for removal)
     *
     * @var string
     */
    protected $oldPath;

    /**
     * Name
     *
     * @var string
     */
    protected $rename;

    /**
     * Unlink (set the subject image field to null)
     *
     * @var bool
     */
    protected $unlink;


    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    /**
     * {@inheritdoc}
     */
    public function hasKey()
    {
        return 0 < strlen($this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile()
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        if (!$this->hasRename()) {
            if ($this->hasPath()) {
                $this->rename = pathinfo($this->path, PATHINFO_BASENAME);
            } elseif ($file instanceof UploadedFile) {
                $this->rename = $file->getClientOriginalName();
            } elseif ($file instanceof File) {
                $this->rename = $file->getBasename();
            }
        }

        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath()
    {
        return null !== $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOldPath()
    {
        return null !== $this->oldPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getOldPath()
    {
        return $this->oldPath;
    }

    /**
     * {@inheritdoc}
     */
    public function setOldPath($oldPath)
    {
        $this->oldPath = $oldPath;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function shouldBeRenamed()
    {
        return (bool)($this->hasPath() && $this->guessFilename() != pathinfo($this->getPath(), PATHINFO_BASENAME));
    }

    /**
     * {@inheritdoc}
     */
    public function guessExtension()
    {
        $extension = null;
        if ($this->hasFile()) {
            $extension = $this->file->guessExtension();
        } elseif ($this->hasPath()) {
            $extension = pathinfo($this->getPath(), PATHINFO_EXTENSION);
        }
        $extension = strtolower($extension);
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        return $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function guessFilename()
    {
        // Extension
        $extension = $this->guessExtension();

        // Filename
        $filename = null;
        if ($this->hasRename()) {
            $filename = Urlizer::transliterate(pathinfo($this->rename, PATHINFO_FILENAME));
        } elseif ($this->hasFile()) {
            $filename = pathinfo($this->file->getFilename(), PATHINFO_FILENAME);
        } elseif ($this->hasPath()) {
            $filename = pathinfo($this->path, PATHINFO_FILENAME);
        }

        if ($filename !== null && $extension !== null) {
            return $filename . '.' . $extension;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRename()
    {
        return 0 < strlen($this->rename);
    }

    /**
     * {@inheritdoc}
     */
    public function getRename()
    {
        return $this->hasRename() ? $this->rename : $this->guessFilename();
    }

    /**
     * {@inheritdoc}
     */
    public function setRename($rename)
    {
        if ($rename !== $this->rename) {
            $this->updatedAt = new \DateTime();
        }
        $this->rename = $rename;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnlink()
    {
        return $this->unlink;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnlink($unlink)
    {
        $this->unlink = (bool) $unlink;
        return $this;
    }
}
