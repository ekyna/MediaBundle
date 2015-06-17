<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\AdminBundle\Model\AbstractTranslation;

/**
 * Class GalleryTranslation
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryTranslation extends AbstractTranslation
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;


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
     * Sets the title.
     *
     * @param string $title
     * @return GalleryTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the description.
     *
     * @param string $description
     * @return GalleryTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
