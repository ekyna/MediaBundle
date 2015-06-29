<?php

namespace Ekyna\Bundle\MediaBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\MediaBundle\Entity\FolderRepository;
use Ekyna\Bundle\MediaBundle\Event\MediaEvent;
use Ekyna\Bundle\MediaBundle\Event\MediaEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MediaEventListener
 * @package Ekyna\Bundle\MediaBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaEventListener implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var FolderRepository
     */
    private $folderRepository;


    /**
     * Constructor.
     *
     * @param ObjectManager    $manager
     * @param FolderRepository $repository
     */
    public function __construct(ObjectManager $manager, FolderRepository $repository)
    {
        $this->manager          = $manager;
        $this->folderRepository = $repository;
    }

    /**
     * Pre delete event handler.
     *
     * @param MediaEvent $event
     */
    public function onPreDelete(MediaEvent $event)
    {
        $media = $event->getMedia();
        $root  = $this->folderRepository->findRoot();

        if ($media->getFolder() !== $root) {
            /* Move media to the root folder so that the current folder can be deleted */
            $media->setFolder($this->folderRepository->findRoot());

            $this->manager->persist($media);
            $this->manager->flush($media);
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            MediaEvents::PRE_DELETE => array('onPreDelete', 1024),
        );
    }
}
