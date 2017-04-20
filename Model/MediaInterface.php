<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model as RM;

/**
 * Interface MediaInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MediaTranslationInterface translate($locale = null, $create = false)
 * @method MediaTranslationInterface[] getTranslations()
 */
interface MediaInterface extends
    RM\UploadableInterface,
    RM\TranslatableInterface,
    RM\TaggedEntityInterface
{
    /**
     * Sets the folder.
     *
     * @param FolderInterface|null $folder
     *
     * @return $this|MediaInterface
     */
    public function setFolder(FolderInterface $folder = null): MediaInterface;

    /**
     * Returns the folder.
     *
     * @return FolderInterface|null
     */
    public function getFolder(): ?FolderInterface;

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return $this|MediaInterface
     */
    public function setType(string $type): MediaInterface;

    /**
     * Returns the type.
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return $this|MediaInterface
     */
    public function setTitle(string $title): MediaInterface;

    /**
     * Returns the title.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Sets the description.
     *
     * @param string|null $description
     *
     * @return $this|MediaInterface
     */
    public function setDescription(string $description = null): MediaInterface;

    /**
     * Returns the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;
}
