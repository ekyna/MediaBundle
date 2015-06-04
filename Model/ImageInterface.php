<?php

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Interface ImageInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ImageInterface extends UploadableInterface
{
    /**
     * Sets the image alt.
     * 
     * @return string
     */
    public function setAlt($alt);

    /**
     * Returns the image alt.
     *
     * @return string
     */
    public function getAlt();
}
