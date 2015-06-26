<?php

namespace Ekyna\Bundle\MediaBundle\Listener;

use Ekyna\Bundle\CoreBundle\Uploader\UploaderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaListener
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaListener
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
     * Pre persist event handler.
     *
     * @param MediaInterface $media
     */
    public function prePersist(MediaInterface $media)
    {
        $this->uploader->prepare($media);
    }

    /**
     * Post persist event handler.
     *
     * @param MediaInterface $media
     */
    public function postPersist(MediaInterface $media)
    {
        $this->uploader->upload($media);
    }

    /**
     * Pre update event handler.
     *
     * @param MediaInterface $media
     */
    public function preUpdate(MediaInterface $media)
    {
        $this->uploader->prepare($media);
    }

    /**
     * Post update event handler.
     *
     * @param MediaInterface $media
     */
    public function postUpdate(MediaInterface $media)
    {
        $this->uploader->upload($media);
    }

    /**
     * Pre remove event handler.
     *
     * @param MediaInterface $media
     */
    public function preRemove(MediaInterface $media)
    {
        $media->setOldPath($media->getPath());
    }

    /**
     * Post remove event handler.
     *
     * @param MediaInterface $media
     */
    public function postRemove(MediaInterface $media)
    {
        $this->uploader->remove($media);
    }
}
