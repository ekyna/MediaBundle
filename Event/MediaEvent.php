<?php

namespace Ekyna\Bundle\MediaBundle\Event;

use Ekyna\Bundle\AdminBundle\Event\ResourceEvent;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaEvent
 * @package Ekyna\Bundle\MediaBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaEvent extends ResourceEvent
{
    /**
     * Constructor.
     *
     * @param MediaInterface $media
     */
    public function __construct(MediaInterface $media)
    {
        $this->setResource($media);
    }

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->getResource();
    }
}
