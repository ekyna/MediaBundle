<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Trait MediaSubjectTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait MediaSubjectTrait
{
    protected ?MediaInterface $media = null;


    /**
     * @return $this|MediaSubjectInterface
     */
    public function setMedia(?MediaInterface $media): MediaSubjectInterface
    {
        $this->media = $media;

        return $this;
    }

    public function getMedia(): ?MediaInterface
    {
        return $this->media;
    }
}
