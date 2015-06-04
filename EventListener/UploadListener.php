<?php

namespace Ekyna\Bundle\MediaBundle\EventListener;

use Oneup\UploaderBundle\Event\PostUploadEvent;
//use Oneup\UploaderBundle\Event\PreUploadEvent;
use Oneup\UploaderBundle\UploadEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class UploadListener
 * @package Ekyna\Bundle\MediaBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class UploadListener implements EventSubscriberInterface
{
    /**
     * Post upload event handler (returns the upload key).
     *
     * @param PostUploadEvent $event
     */
    public function onPostUpload(PostUploadEvent $event)
    {
        $response = $event->getResponse();

        $key = null;

        $file = $event->getFile();
        if ($file instanceof File) {
            $key = $file->getFileName();
        }

        $response['upload_key'] = $key;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            UploadEvents::POST_UPLOAD => array('onPostUpload'),
        );
    }
}
