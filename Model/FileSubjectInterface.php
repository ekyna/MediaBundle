<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\MediaBundle\Entity\File;

/**
 * Interface FileSubjectInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface FileSubjectInterface
{
    /**
     * Returns the file.
     *
     * @return File
     */
    public function getFile();

    /**
     * Sets the file.
     *
     * @param File $file
     * @return FileSubjectInterface|$this
     */
    public function setFile(File $file = null);
}
