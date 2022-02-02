<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\AbstractResource;
use Ekyna\Component\Resource\Model\TreeTrait;

/**
 * Class Folder
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Folder extends AbstractResource implements FolderInterface
{
    use TreeTrait;

    private ?string $name = null;
    /** @var Collection|MediaInterface[] */
    protected Collection $medias;
    /** Non persisted - Js usage. */
    protected bool $active = false;

    public function __construct()
    {
        $this->medias = new ArrayCollection();

        $this->initializeNode();
    }

    public function __toString(): string
    {
        return $this->name ?: 'New folder';
    }

    public function setName(?string $name): FolderInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function hasMedia(MediaInterface $media): bool
    {
        return $this->medias->contains($media);
    }

    public function addMedia(MediaInterface $media): FolderInterface
    {
        if (!$this->hasMedia($media)) {
            $this->medias->add($media);
        }

        return $this;
    }

    public function removeMedia(MediaInterface $media): FolderInterface
    {
        if ($this->hasMedia($media)) {
            $this->medias->removeElement($media);
        }

        return $this;
    }

    public function hasMedias(): bool
    {
        return 0 < $this->children->count();
    }

    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function setActive(bool $active): FolderInterface
    {
        $this->active = $active;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }
}
