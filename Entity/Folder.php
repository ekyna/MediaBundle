<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class Folder
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Folder implements FolderInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    protected $left;

    /**
     * @var integer
     */
    protected $right;

    /**
     * @var integer
     */
    protected $root;

    /**
     * @var integer
     */
    protected $level;

    /**
     * @var Folder
     */
    protected $parent;

    /**
     * @var ArrayCollection|FolderInterface[]
     */
    protected $children;

    /**
     * @var ArrayCollection|MediaInterface[]
     */
    protected $medias;

    /**
     * @var bool
     */
    protected $active = false;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->medias = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @inheritdoc
     */
    public function setRight($right)
    {
        $this->right = $right;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @inheritdoc
     */
    public function setRoot($root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @inheritdoc
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @inheritdoc
     */
    public function setParent(FolderInterface $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasChild(FolderInterface $child)
    {
        return $this->children->contains($child);
    }

    /**
     * @inheritdoc
     */
    public function addChild(FolderInterface $child)
    {
        if (!$this->hasChild($child)) {
            $this->children->add($child);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeChild(FolderInterface $child)
    {
        if ($this->hasChild($child)) {
            $this->children->removeElement($child);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        return 0 < $this->children->count();
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets the medias.
     *
     * @param ArrayCollection|MediaInterface[] $medias
     * @return Folder
     */
    public function setMedias(ArrayCollection $medias)
    {
        $this->medias = $medias;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasMedia(MediaInterface $media)
    {
        return $this->medias->contains($media);
    }

    /**
     * @inheritdoc
     */
    public function addMedia(MediaInterface $media)
    {
        if (!$this->hasMedia($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeMedia(MediaInterface $media)
    {
        if ($this->hasMedia($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasMedias()
    {
        return 0 < $this->children->count();
    }

    /**
     * Returns the medias.
     *
     * @return ArrayCollection|MediaInterface[]
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @inheritdoc
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * [Serializer] Returns the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getId();
    }

    /**
     * [Serializer] Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getName();
    }

    /**
     * [Serializer] Returns the icon.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'folder';
    }

    /**
     * [Serializer/Tree] Folder icon.
     *
     * @return string
     */
    public function getFolder()
    {
        return true;
    }

    /**
     * [Serializer/Tree] Active folder.
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
