<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model as Media;

/**
 * Class GalleryImage
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryImage implements Media\GalleryImageInterface
{
    use Media\ImageSubjectTrait,
        Core\SortableTrait,
        Core\TaggedEntityTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Media\GalleryInterface
     */
    protected $gallery;


    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the gallery.
     *
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * {@inheritdoc}
     */
    public function setGallery(Media\GalleryInterface $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->image->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlt()
    {
        return $this->image->getAlt();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTags()
    {
        $tags = [$this->getEntityTag()];
        if (null !== $this->image) {
            $tags[] = $this->image->getEntityTag();
        }
        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.gallery';
    }
}
