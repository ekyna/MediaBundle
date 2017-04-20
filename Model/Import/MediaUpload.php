<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model\Import;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaUpload
 * @package Ekyna\Bundle\MediaBundle\Model\Import
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaUpload
{
    /**
     * @var ArrayCollection|MediaInterface[]
     */
    protected $medias;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->medias = new ArrayCollection();
    }

    /**
     * Adds the media.
     *
     * @param MediaInterface $media
     *
     * @return MediaUpload
     */
    public function addMedia(MediaInterface $media): MediaUpload
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    /**
     * Removes the media.
     *
     * @param MediaInterface $media
     *
     * @return MediaUpload
     */
    public function removeMedia(MediaInterface $media): MediaUpload
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    /**
     * Returns the medias.
     *
     * @param ArrayCollection $medias
     *
     * @return MediaUpload
     */
    public function setMedias(ArrayCollection $medias): MediaUpload
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * Returns the medias.
     *
     * @return ArrayCollection|MediaInterface[]
     */
    public function getMedias(): ArrayCollection
    {
        return $this->medias;
    }
}
