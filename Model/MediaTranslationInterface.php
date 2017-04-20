<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface MediaTranslationInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface MediaTranslationInterface extends TranslationInterface
{
    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Sets the title.
     *
     * @param string|null $title
     *
     * @return $this|MediaTranslationInterface
     */
    public function setTitle(string $title = null): MediaTranslationInterface;

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription(): ?string;

    /**
     * Sets the description.
     *
     * @param string|null $description
     *
     * @return $this|MediaTranslationInterface
     */
    public function setDescription(string $description = null): MediaTranslationInterface;
}
