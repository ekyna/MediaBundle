<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Component\Resource\Model as RM;
use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model;

/**
 * Class Media
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MediaTranslation translate($locale = null, $create = false)
 */
class Media implements Model\MediaInterface
{
    use Core\UploadableTrait,
        RM\TranslatableTrait,
        RM\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Model\FolderInterface
     */
    protected $folder;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $thumb;

    /**
     * @var string
     */
    protected $front;

    /**
     * @var string
     */
    protected $player;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initializeTranslations();
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->guessFilename();
    }

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
     * {@inheritdoc}
     */
    public function setFolder(Model\FolderInterface $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
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
    public function setThumb($url)
    {
        $this->thumb = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * {@inheritdoc}
     */
    public function setFront($url)
    {
        $this->front = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFront()
    {
        return $this->front;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlayer($url)
    {
        $this->player = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * {@inheritdoc}
     */
    public function getFolderId()
    {
        return $this->folder ? $this->folder->getId() : null;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.media';
    }
}
