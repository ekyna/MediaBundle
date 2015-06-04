<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\MediaBundle\Entity\Image;

/**
 * Interface ImageSubjectInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ImageSubjectInterface
{
    /**
     * Returns the image.
     *
     * @return mixed
     */
    public function getImage();

    /**
     * Sets the image.
     *
     * @param Image $image
     * @return ImageSubjectInterface|$this
     */
    public function setImage(Image $image = null);
}
