<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\AdminBundle\Model\TranslatableInterface;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Interface MediaInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface MediaInterface extends
    Core\UploadableInterface,
    Core\SoftDeleteableInterface,
    Core\TaggedEntityInterface,
    TranslatableInterface
{
    /**
     * Sets the folder.
     *
     * @param FolderInterface $folder
     * @return MediaInterface|$this
     */
    public function setFolder(FolderInterface $folder);

    /**
     * Returns the folder.
     *
     * @return FolderInterface
     */
    public function getFolder();

    /**
     * Sets the type.
     *
     * @param string $type
     * @return MediaInterface|$this
     */
    public function setType($type);

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the title.
     *
     * @param string $title
     * @return MediaInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the description.
     *
     * @param string $description
     * @return MediaInterface|$this
     */
    public function setDescription($description);

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the thumb.
     *
     * @param string $thumb
     * @return MediaInterface|$this
     */
    public function setThumb($thumb);

    /**
     * Returns the thumb.
     *
     * @return string
     */
    public function getThumb();

    /**
     * Returns the filename.
     *
     * @return string
     */
    public function getFilename();
}
