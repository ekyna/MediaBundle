<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait UploadableTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait UploadableTrait
{
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
     * Update date
     *
     * @var \DateTime
     */
    protected $updatedAt;


    /**
     * Returns whether the uploadable has an upload key.
     *
     * @return bool
     */
    public function hasKey()
    {
        return 0 < strlen($this->key);
    }

    /**
     * Returns the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Sets the key.
     *
     * @param string $key
     * @return UploadableTrait
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Has file.
     *
     * @return boolean
     */
    public function hasFile()
    {
        return null !== $this->file;
    }

    /**
     * Get file.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param File $file
     * @return UploadableTrait|$this
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
     * Has path.
     *
     * @return boolean
     */
    public function hasPath()
    {
        return null !== $this->path;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param string $path
     * @return UploadableTrait|$this
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Has old path.
     *
     * @return boolean
     */
    public function hasOldPath()
    {
        return null !== $this->oldPath;
    }

    /**
     * Get old path.
     *
     * @return string
     */
    public function getOldPath()
    {
        return $this->oldPath;
    }

    /**
     * Set old path.
     *
     * @param string $oldPath
     * @return UploadableTrait|$this
     */
    public function setOldPath($oldPath)
    {
        $this->oldPath = $oldPath;

        return $this;
    }

    /**
     * Returns whether the uploadable should be renamed or not.
     *
     * @return boolean
     */
    public function shouldBeRenamed()
    {
        return (bool)($this->hasPath() && $this->guessFilename() != pathinfo($this->getPath(), PATHINFO_BASENAME));
    }

    /**
     * Guess file extension.
     *
     * @return string
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
     * Guess file name.
     *
     * @return string
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
     * Returns whether the uploadable has a rename or not.
     *
     * @return boolean
     */
    public function hasRename()
    {
        return 0 < strlen($this->rename);
    }

    /**
     * Get rename.
     *
     * @return string
     */
    public function getRename()
    {
        return $this->hasRename() ? $this->rename : $this->guessFilename();
    }

    /**
     * Set rename.
     *
     * @param string $rename
     * @return UploadableTrait|$this
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
     * Returns the unlink.
     *
     * @return boolean
     */
    public function getUnlink()
    {
        return $this->unlink;
    }

    /**
     * Sets the unlink.
     *
     * @param boolean $unlink
     * @return UploadableTrait
     */
    public function setUnlink($unlink)
    {
        $this->unlink = (bool) $unlink;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UploadableTrait|$this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
