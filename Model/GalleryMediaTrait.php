<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model\SortableTrait;

/**
 * Trait GalleryMediaTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait GalleryMediaTrait
{
    use SortableTrait;

    /**
     * @var MediaInterface
     */
    protected $media;


    /**
     * Sets the media.
     *
     * @param MediaInterface $media
     * @return MediaSubjectInterface|$this
     */
    public function setMedia(MediaInterface $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }
}
