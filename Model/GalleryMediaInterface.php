<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Interface GalleryImageInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface GalleryMediaInterface extends MediaSubjectInterface, Core\SortableInterface, Core\TaggedEntityInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();

    /**
     * Returns the gallery.
     *
     * @return GalleryInterface
     */
    public function getGallery();

    /**
     * Sets the gallery.
     *
     * @param GalleryInterface $gallery
     * @return MediaSubjectInterface|$this
     */
    public function setGallery(GalleryInterface $gallery = null);

    /**
     * Media path getter alias.
     *
     * @return string
     */
    public function getPath();

    /**
     * Media title getter alias.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Media title getter alias.
     *
     * @return string
     */
    public function getDescription();
}
