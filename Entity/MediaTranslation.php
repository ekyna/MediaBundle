<?php

declare(strict_types=1);

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
    protected ?string $title = null;
    protected ?string $description = null;


    /**
     * @inheritDoc
     */
    public function setTitle(string $title = null): MediaTranslationInterface
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $description = null): MediaTranslationInterface
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
