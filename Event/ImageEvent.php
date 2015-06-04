<?php

namespace Ekyna\Bundle\MediaBundle\Event;

use Ekyna\Bundle\MediaBundle\Model\ImageInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ImageEvent
 * @package Ekyna\Bundle\MediaBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageEvent extends Event
{
    /**
     * @var ImageInterface
     */
    protected $image;

    /**
     * Constructor.
     * 
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * Returns the image.
     * 
     * @return ImageInterface
     */
    public function getImage()
    {
        return $this->image;
    }
}
