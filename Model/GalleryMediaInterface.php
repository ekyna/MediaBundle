<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model\SortableInterface;

/**
 * Interface GalleryMediaInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface GalleryMediaInterface extends SortableInterface
{
    /**
     * @return $this|GalleryMediaInterface
     */
    public function setMedia(MediaInterface $media): GalleryMediaInterface;

    public function getMedia(): ?MediaInterface;
}
