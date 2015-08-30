<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\CoreBundle\Model\SortableInterface;

/**
 * Interface GalleryMediaInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface GalleryMediaInterface extends SortableInterface
{
    /**
     * Sets the media.
     *
     * @param MediaInterface $media
     * @return MediaSubjectInterface|$this
     */
    public function setMedia(MediaInterface $media);

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia();
}