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


    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return $this|FolderInterface
     */
    public function setName(string $name): FolderInterface;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Returns whether the folder has the given media or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public function hasMedia(MediaInterface $media): bool;

    /**
     * Adds the media.
     *
     * @param MediaInterface $media
     *
     * @return $this|FolderInterface
     */
    public function addMedia(MediaInterface $media): FolderInterface;

    /**
     * Removes the media.
     *
     * @param MediaInterface $media
     *
     * @return $this|FolderInterface
     */
    public function removeMedia(MediaInterface $media): FolderInterface;

    /**
     * Returns whether the folder has medias or not.
     *
     * @return bool
     */
    public function hasMedias(): bool;

    /**
     * Returns the medias.
     *
     * @return Collection|MediaInterface[]
     */
    public function getMedias(): Collection;

    /**
     * Sets whether this folder is active.
     * Non persisted - Js usage.
     *
     * @param bool $active
     *
     * @return $this|FolderInterface
     */
    public function setActive(bool $active): FolderInterface;

    /**
     * Returns whether this folder is active.
     * Non persisted - Js usage.
     *
     * @return bool
     */
    public function getActive(): bool;
}
