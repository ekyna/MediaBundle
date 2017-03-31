<?php

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Interface MediaTranslationInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface MediaTranslationInterface
{
    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return $this|MediaTranslationInterface
     */
    public function setTitle($title);

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the description.
     *
     * @param string $description
     *
     * @return $this|MediaTranslationInterface
     */
    public function setDescription($description);
}
