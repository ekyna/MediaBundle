<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\TreeTrait;

/**
 * Class Folder
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Folder implements FolderInterface
{
    use TreeTrait;

    private ?int    $id   = null;
    private ?string $name = null;
    /** @var Collection|MediaInterface[] */
    protected Collection $medias;
    /** Non persisted - Js usage. */
    protected bool $active = false;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->medias = new ArrayCollection();

        $this->initializeNode();
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?: 'New folder';
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): FolderInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function hasMedia(MediaInterface $media): bool
    {
        return $this->medias->contains($media);
    }

    /**
     * @inheritDoc
     */
    public function addMedia(MediaInterface $media): FolderInterface
    {
        if (!$this->hasMedia($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeMedia(MediaInterface $media): FolderInterface
    {
        if ($this->hasMedia($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasMedias(): bool
    {
        return 0 < $this->children->count();
    }

    /**
     * @inheritDoc
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    /**
     * @inheritDoc
     */
    public function setActive(bool $active): FolderInterface
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getActive(): bool
    {
        return $this->active;
    }
}
