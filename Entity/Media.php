<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Media
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Model\MediaTranslationInterface translate($locale = null, $create = false)
 * @method Model\MediaTranslationInterface[] getTranslations()
 */
class Media implements Model\MediaInterface
{
    use RM\UploadableTrait;
    use RM\TaggedEntityTrait;
    use RM\TranslatableTrait;

    protected ?int $id = null;
    protected ?Model\FolderInterface $folder = null;
    protected ?string $type = null;


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
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setFolder(Model\FolderInterface $folder = null): Model\MediaInterface
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFolder(): ?Model\FolderInterface
    {
        return $this->folder;
    }

    /**
     * @inheritDoc
     */
    public function setType($type): Model\MediaInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title): Model\MediaInterface
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->translate()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description = null): Model\MediaInterface
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->translate()->getDescription();
    }

    /**
     * @inheritDoc
     */
    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_media.media';
    }
}
