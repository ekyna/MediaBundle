<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

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
    public function __toString(): string
    {
        // TODO Return translation title ?
        return $this->guessFilename() ?: 'New media';
    }

    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setFolder(Model\FolderInterface $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * @inheritdoc
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.media';
    }
}
