<?php

namespace Ekyna\Bundle\MediaBundle\EventListener;

use Ekyna\Bundle\MediaBundle\Event\ImageEvent;
use Ekyna\Bundle\MediaBundle\Event\ImageEvents;
use Ekyna\Bundle\MediaBundle\Uploader\UploaderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ImageEventSubscriber
 * @package Ekyna\Bundle\MediaBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var UploaderInterface
     */
    private $uploader;


    /**
     * @param UploaderInterface $uploader
     */
    public function __construct(UploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Image upload event handler.
     * 
     * @param ImageEvent $event
     */
    public function onImageUpload(ImageEvent $event)
    {
        $image = $event->getImage();
        
        $this->uploader->prepare($image);
        $this->uploader->upload($image);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ImageEvents::UPLOAD => array('onImageUpload', 0),
        );
    }
}
