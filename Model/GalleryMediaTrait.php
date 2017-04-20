<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model\SortableTrait;

/**
 * Trait GalleryMediaTrait
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
trait GalleryMediaTrait
{
    use SortableTrait;

    protected ?MediaInterface $media = null;

    /**
     * @return $this|GalleryMediaInterface
     */
    public function setMedia(MediaInterface $media): GalleryMediaInterface
    {
        $this->media = $media;

        return $this;
    }

    public function getMedia(): ?MediaInterface
    {
        return $this->media;
    }
}
