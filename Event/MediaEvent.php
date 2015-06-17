<?php

namespace Ekyna\Bundle\MediaBundle\Event;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MediaEvent
 * @package Ekyna\Bundle\MediaBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaEvent extends Event
{
    /**
     * @var MediaInterface
     */
    protected $media;

    /**
     * Constructor.
     *
     * @param MediaInterface $media
     */
    public function __construct(MediaInterface $media)
    {
        $this->media = $media;
    }

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }
}
