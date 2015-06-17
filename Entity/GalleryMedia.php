<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model\GalleryInterface;
use Ekyna\Bundle\MediaBundle\Model\GalleryMediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaSubjectTrait;

/**
 * Class GalleryImage
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryMedia implements GalleryMediaInterface
{
    use MediaSubjectTrait,
        Core\SortableTrait,
        Core\TaggedEntityTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var GalleryInterface
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
    public function setGallery(GalleryInterface $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->media->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->media->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->media->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTags()
    {
        $tags = [$this->getEntityTag()];
        if (null !== $this->media) {
            $tags[] = $this->media->getEntityTag();
        }
        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.gallery_media';
    }
}
