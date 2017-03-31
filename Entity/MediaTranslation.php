<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model\MediaTranslationInterface;
use Ekyna\Component\Resource\Model\AbstractTranslation;

/**
 * Class MediaTranslation
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTranslation extends AbstractTranslation implements MediaTranslationInterface
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
     * @return MediaTranslation
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
     * @return MediaTranslation
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
