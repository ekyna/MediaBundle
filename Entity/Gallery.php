<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\AdminBundle\Model\TranslatableTrait;
use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model\GalleryInterface;
use Ekyna\Bundle\MediaBundle\Model\GalleryMediaInterface;

/**
 * Class Gallery
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method GalleryTranslation translate($locale = null, $create = false)
 */
class Gallery implements GalleryInterface
{
    use TranslatableTrait,
        Core\TimestampableTrait,
        Core\TaggedEntityTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var ArrayCollection|GalleryMediaInterface[]
     */
    private $medias;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->medias = new ArrayCollection();
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setMedias(ArrayCollection $medias)
    {
        foreach ($medias as $media) {
            $media->setGallery($this);
        }
        $this->medias = $medias;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMedia(GalleryMediaInterface $media)
    {
        return $this->medias->contains($media);
    }

    /**
     * {@inheritdoc}
     */
    public function addMedia(GalleryMediaInterface $media)
    {
        if (!$this->hasMedia($media)) {
            $media->setGallery($this);
            $this->medias->add($media);
            $this->setUpdatedAt(new \DateTime());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMedia(GalleryMediaInterface $media)
    {
        if ($this->hasMedia($media)) {
            $media->setGallery(null);
            $this->medias->removeElement($media);
            $this->setUpdatedAt(new \DateTime());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTags()
    {
        $tags = [$this->getEntityTag()];
        foreach ($this->medias as $media) {
            $tags = array_merge($tags, $media->getEntityTags());
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
