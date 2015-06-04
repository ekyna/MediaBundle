<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

/**
 * Class AbstractMedia
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractMedia extends AbstractUploadable
{
    /**
     * @var Folder
     */
    protected $folder;

    /**
     * Sets the folder.
     *
     * @param Folder $folder
     * @return AbstractMedia
     */
    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Returns the folder.
     *
     * @return Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
