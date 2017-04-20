<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Listener;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaListener
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaListener
{
    /**
     * Pre persist event handler.
     *
     * @param MediaInterface $media
     */
    public function prePersist(MediaInterface $media): void
    {
        $this->cleanTranslations($media);
    }

    /**
     * Pre update event handler.
     *
     * @param MediaInterface $media
     */
    public function preUpdate(MediaInterface $media): void
    {
        $this->cleanTranslations($media);
    }

    /**
     * Removes empty translations
     *
     * @param MediaInterface $media
     */
    private function cleanTranslations(MediaInterface $media): void
    {
        foreach ($media->getTranslations() as $trans) {
            if (empty($trans->getTitle()) && empty($trans->getDescription())) {
                $media->removeTranslation($trans);
            }
        }
    }
}
