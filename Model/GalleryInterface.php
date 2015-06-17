<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\AdminBundle\Model\TranslatableInterface;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Interface GalleryInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface GalleryInterface extends TranslatableInterface, Core\TimestampableInterface, Core\TaggedEntityInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();

    /**
     * Sets the title.
     *
     * @param string $title
     * @return GalleryInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the description.
     *
     * @param string $description
     * @return GalleryInterface|$this
     */
    public function setDescription($description);

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the medias.
     *
     * @param ArrayCollection|GalleryMediaInterface[] $medias
     * @return GalleryInterface|$this
     */
    public function setMedias(ArrayCollection $medias);

    /**
     * Returns whether the gallery contains the media or not.
     *
     * @param GalleryMediaInterface $media
     * @return bool
     */
    public function hasMedia(GalleryMediaInterface $media);

    /**
     * Adds the media.
     *
     * @param GalleryMediaInterface $media
     * @return GalleryInterface|$this
     */
    public function addMedia(GalleryMediaInterface $media);

    /**
     * Removes the media.
     *
     * @param GalleryMediaInterface $media
     * @return GalleryInterface|$this
     */
    public function removeMedia(GalleryMediaInterface $media);

    /**
     * Returns the medias.
     *
     * @return ArrayCollection|GalleryMediaInterface[]
     */
    public function getMedias();
}
