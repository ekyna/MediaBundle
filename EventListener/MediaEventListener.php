<?php

namespace Ekyna\Bundle\MediaBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FolderRepository
     */
    private $folderRepository;


    /**
     * Constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FolderRepository       $repository
     */
    public function __construct(EntityManagerInterface $entityManager, FolderRepository $repository)
    {
        $this->entityManager    = $entityManager;
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

            $this->entityManager->persist($media);
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $this->entityManager->flush($media);
        }
    }

    /**
     * {@inheritdoc}
     */
    static public function getSubscribedEvents()
    {
        return array(
            MediaEvents::PRE_DELETE => array('onPreDelete',  1024),
        );
    }
}
