<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface MediaInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MediaTranslationInterface translate($locale = null, $create = false)
 */
interface MediaInterface extends
    Core\UploadableInterface,
    RM\TranslatableInterface,
    RM\TaggedEntityInterface
{
    /**
     * Sets the folder.
     *
     * @param FolderInterface $folder
     *
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
     *
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
     *
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
     *
     * @return MediaInterface|$this
     */
    public function setDescription($description);

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();
}
