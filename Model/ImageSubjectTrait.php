<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\MediaBundle\Entity\Image;

/**
 * Trait ImageSubjectTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ImageSubjectTrait
{
    /**
     * @var Image
     */
    protected $image;

    /**
     * Returns the image.
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image.
     *
     * @param Image $image
     * @return ImageSubjectInterface|$this
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;

        return $this;
    }
}
