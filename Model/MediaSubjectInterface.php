<?php

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Interface MediaSubjectInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface MediaSubjectInterface
{
    /**
     * Returns the file.
     *
     * @return MediaInterface
     */
    public function getMedia();

    /**
     * Sets the file.
     *
     * @param MediaInterface $media
     * @return MediaSubjectInterface|$this
     */
    public function setMedia(MediaInterface $media = null);
}
