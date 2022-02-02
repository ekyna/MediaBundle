<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model\MediaTranslationInterface;
use Ekyna\Component\Resource\Model\AbstractTranslation;

/**
 * Class MediaTranslation
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTranslation extends AbstractTranslation implements MediaTranslationInterface
{
    protected ?string $title       = null;
    protected ?string $description = null;

    public function setTitle(?string $title): MediaTranslationInterface
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): MediaTranslationInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
