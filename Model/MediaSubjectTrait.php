<?php

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Trait MediaSubjectTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait MediaSubjectTrait
{
    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the media.
     *
     * @param MediaInterface $media
     * @return MediaSubjectInterface|$this
     */
    public function setMedia(MediaInterface $media = null)
    {
        $this->media = $media;

        return $this;
    }
}
