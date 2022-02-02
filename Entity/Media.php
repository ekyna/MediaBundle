<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model;
use Ekyna\Component\Resource\Model as RM;
use Ekyna\Component\Resource\Model\AbstractResource;

/**
 * Class Media
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Model\MediaTranslationInterface translate($locale = null, $create = false)
 * @method Model\MediaTranslationInterface[] getTranslations()
 */
class Media extends AbstractResource implements Model\MediaInterface
{
    use RM\UploadableTrait;
    use RM\TaggedEntityTrait;
    use RM\TranslatableTrait;

    protected ?Model\FolderInterface $folder = null;
    protected ?string                $type   = null;

    public function __toString(): string
    {
        // TODO Return translation title ?
        return $this->guessFilename() ?: 'New media';
    }

    public function setFolder(?Model\FolderInterface $folder): Model\MediaInterface
    {
        $this->folder = $folder;

        return $this;
    }

    public function getFolder(): ?Model\FolderInterface
    {
        return $this->folder;
    }

    public function setType(?string $type): Model\MediaInterface
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setTitle(?string $title): Model\MediaInterface
    {
        $this->translate()->setTitle($title);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->translate()->getTitle();
    }

    public function setDescription(?string $description): Model\MediaInterface
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->translate()->getDescription();
    }

    public static function getEntityTagPrefix(): string
    {
        return 'ekyna_media.media';
    }
}
