<?php

namespace Ekyna\Bundle\MediaBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Ekyna\Bundle\MediaBundle\Model\FileInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\MediaBundle\Uploader\UploaderInterface;

/**
 * Class FileListener
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FileListener implements EventSubscriber
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
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof FileInterface) {
            $this->uploader->prepare($entity);
        }
    }

    /**
     * Post persist event handler.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof FileInterface) {
            $this->uploader->upload($entity);
        }
    }

    /**
     * Pre update event handler.
     *
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof FileInterface) {
            $this->uploader->prepare($entity);
        }
    }

    /**
     * Post update event handler.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof FileInterface) {
            $this->uploader->upload($entity);
        }
    }

    /**
     * Pre remove event handler.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof FileInterface) {
            $entity->setOldPath($entity->getPath());
        }
    }

    /**
     * Post remove event handler.
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof FileInterface) {
            $this->uploader->remove($entity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::preRemove,
            Events::postRemove,
        );
    }
}
