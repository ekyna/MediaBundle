<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\AdminBundle\Model\TranslatableTrait;
use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class Media
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MediaTranslation translate($locale = null, $create = false)
 */
class Media implements MediaInterface
{
    use Core\SoftDeleteableTrait,
        Core\TaggedEntityTrait,
        TranslatableTrait;

    use Core\UploadableTrait {
        setFile as uploadableSetFile;
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var FolderInterface
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
     * Constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
    public function setFolder(FolderInterface $folder)
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
    public function setFile(File $file = null)
    {
        if (null !== $file) {
            $this->setType(MediaTypes::guessByMimeType($file->getMimeType()));
        }

        return $this->uploadableSetFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
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
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.media';
    }
}
