<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Model\TreeInterface;

/**
 * Interface FolderInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Collection|FolderInterface[] getChildren()
 * @method FolderInterface|null getParent()
 */
interface FolderInterface extends TreeInterface
{
    public const ROOT = 'Medias';

    public function setName(?string $name): FolderInterface;

    public function getName(): ?string;

    /**
     * Returns whether the folder has the given media or not.
     */
    public function hasMedia(MediaInterface $media): bool;

    public function addMedia(MediaInterface $media): FolderInterface;

    public function removeMedia(MediaInterface $media): FolderInterface;

    /**
     * Returns whether the folder has medias or not.
     */
    public function hasMedias(): bool;

    /**
     * @return Collection<MediaInterface>
     */
    public function getMedias(): Collection;

    /**
     * Sets whether this folder is active.
     * Non persisted - Js usage.
     */
    public function setActive(bool $active): FolderInterface;

    /**
     * Returns whether this folder is active.
     * Non persisted - Js usage.
     */
    public function getActive(): bool;
}
