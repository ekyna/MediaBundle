<?php

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Trait GallerySubjectTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait GallerySubjectTrait
{
    /**
     * @var GalleryInterface
     */
    protected $gallery;

    /**
     * Sets the gallery.
     *
     * @param GalleryInterface $gallery
     * @return GallerySubjectInterface|$this
     */
    public function setGallery(GalleryInterface $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Returns the gallery.
     *
     * @return GalleryInterface
     */
    public function getGallery()
    {
        return $this->gallery;
    }
}