<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface FolderInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface FolderInterface
{
    const ROOT = 'Medias';

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     * @return FolderInterface|$this
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the left.
     *
     * @param int $left
     * @return FolderInterface|$this
     */
    public function setLeft($left);

    /**
     * Returns the left.
     *
     * @return int
     */
    public function getLeft();

    /**
     * Sets the right.
     *
     * @param int $right
     * @return FolderInterface|$this
     */
    public function setRight($right);

    /**
     * Returns the right.
     *
     * @return int
     */
    public function getRight();

    /**
     * Sets the root.
     *
     * @param int $root
     * @return FolderInterface|$this
     */
    public function setRoot($root);

    /**
     * Returns the root.
     *
     * @return int
     */
    public function getRoot();

    /**
     * Sets the level.
     *
     * @param int $level
     * @return FolderInterface|$this
     */
    public function setLevel($level);

    /**
     * Returns the level.
     *
     * @return int
     */
    public function getLevel();

    /**
     * Sets the parent.
     *
     * @param FolderInterface $parent
     * @return FolderInterface|$this
     */
    public function setParent(FolderInterface $parent = null);

    /**
     * Returns the parent.
     *
     * @return FolderInterface|$this
     */
    public function getParent();

    /**
     * Sets the children.
     *
     * @param ArrayCollection|FolderInterface[] $children
     * @return FolderInterface|$this
     */
    public function setChildren(ArrayCollection $children);

    /**
     * Returns whether the folder as the child folder or not.
     *
     * @param FolderInterface $child
     * @return bool
     */
    public function hasChild(FolderInterface $child);

    /**
     * Adds the child folder.
     *
     * @param FolderInterface $child
     * @return FolderInterface|$this
     */
    public function addChild(FolderInterface $child);

    /**
     * Removes the child folder.
     *
     * @param FolderInterface $child
     * @return FolderInterface|$this
     */
    public function removeChild(FolderInterface $child);

    /**
     * Returns whether the folder has children or not.
     *
     * @return bool
     */
    public function hasChildren();

    /**
     * Returns the children.
     *
     * @return ArrayCollection|FolderInterface[]
     */
    public function getChildren();

    /**
     * Sets the medias.
     *
     * @param ArrayCollection|MediaInterface[] $medias
     * @return FolderInterface|$this
     */
    public function setMedias(ArrayCollection $medias);

    /**
     * Returns whether the folder has the given media or not.
     *
     * @param MediaInterface $media
     * @return bool
     */
    public function hasMedia(MediaInterface $media);

    /**
     * Adds the media.
     *
     * @param MediaInterface $media
     * @return FolderInterface|$this
     */
    public function addMedia(MediaInterface $media);

    /**
     * Removes the media.
     *
     * @param MediaInterface $media
     * @return FolderInterface|$this
     */
    public function removeMedia(MediaInterface $media);

    /**
     * Returns whether the folder has medias or not.
     *
     * @return bool
     */
    public function hasMedias();

    /**
     * Returns the medias.
     *
     * @return ArrayCollection|MediaInterface[]
     */
    public function getMedias();
}
