<?php

namespace Ekyna\Bundle\MediaBundle\EventListener;

use Ekyna\Bundle\MediaBundle\Entity\ConversionRequest;
use Ekyna\Bundle\MediaBundle\Event\MediaEvents;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\ConversionRequestRepository;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\Resource\Persistence\PersistenceHelperInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as Flysystem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MediaEventSubscriber
 * @package Ekyna\Bundle\MediaBundle\EventListener
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConversionRequestRepository
     */
    protected $requestRepository;

    /**
     * @var PersistenceHelperInterface
     */
    protected $persistenceHelper;

    /**
     * @var Flysystem
     */
    protected $videoFilesystem;


    /**
     * Constructor.
     *
     * @param ConversionRequestRepository $requestRepository
     * @param PersistenceHelperInterface  $persistenceHelper
     * @param Flysystem                   $videoFilesystem
     */
    public function __construct(
        ConversionRequestRepository $requestRepository,
        PersistenceHelperInterface $persistenceHelper,
        Flysystem $videoFilesystem
    ) {
        $this->requestRepository = $requestRepository;
        $this->persistenceHelper = $persistenceHelper;
        $this->videoFilesystem   = $videoFilesystem;
    }

    /**
     * Media insert event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onInsert(ResourceEventInterface $event): void
    {
        $media = $this->getMediaFromResourceEvent($event);

        $this->createConversionRequests($media);
    }

    /**
     * Media update event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onUpdate(ResourceEventInterface $event): void
    {
        $media = $this->getMediaFromResourceEvent($event);

        if ($this->persistenceHelper->isChanged($media, 'path')) {
            $this->removeConvertedVideos($media);
        } elseif (!$this->persistenceHelper->isChanged($media, 'size')) {
            // Neither path or size changed -> abort
            return;
        }

        $this->createConversionRequests($media);
    }

    /**
     * Media delete event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onDelete(ResourceEventInterface $event): void
    {
        $media = $this->getMediaFromResourceEvent($event);

        $this->removeConvertedVideos($media);
    }

    /**
     * Removes the converted videos.
     *
     * @param MediaInterface $media
     */
    protected function removeConvertedVideos(MediaInterface $media): void
    {
        if ($media->getType() !== MediaTypes::VIDEO) {
            return;
        }

        if ($this->persistenceHelper->isChanged($media, 'path')) {
            $path = $this->persistenceHelper->getChangeSet($media, 'path')[0];
        } else {
            $path = $media->getPath();
        }

        $info = pathinfo($path);

        foreach (MediaFormats::getFormatsByType(MediaTypes::VIDEO) as $format) {
            $key = $info['dirname'] . '/' . $info['filename'] . '.' . $format;

            if (!$this->videoFilesystem->has($key)) {
                continue;
            }

            $path = $this->getVideoAdapter()->applyPathPrefix($key);

            @unlink($path);
        }
    }

    /**
     * Creates (or updates) the conversion requests.
     *
     * @param MediaInterface $media
     */
    protected function createConversionRequests(MediaInterface $media): void
    {
        if ($media->getType() !== MediaTypes::VIDEO) {
            return;
        }

        $requests = $this->requestRepository->findByMedia($media);
        $formats  = [];

        foreach (MediaFormats::getFormatsByType(MediaTypes::VIDEO) as $format) {
            foreach ($requests as $request) {
                if (
                    $format === $request->getFormat()
                    && ConversionRequest::STATE_PENDING === $request->getState()
                ) {
                    continue 2;
                }
            }

            $formats[] = $format;
        }

        foreach ($formats as $format) {
            $request = new ConversionRequest();
            $request
                ->setMedia($media)
                ->setFormat($format);

            $this->persistRequest($request);
        }
    }

    /**
     * Persists the conversion request.
     *
     * @param ConversionRequest $request
     */
    protected function persistRequest(ConversionRequest $request): void
    {
        $manager = $this->persistenceHelper->getManager();

        $uow = $manager->getUnitOfWork();

        if (!$this->persistenceHelper->getEventQueue()->isOpened()) {
            $manager->persist($request);

            return;
        }

        if (!($uow->isScheduledForInsert($request) || $uow->isScheduledForUpdate($request))) {
            $manager->persist($request);
        }

        $metadata = $manager->getClassMetadata(get_class($request));
        if ($uow->getEntityChangeSet($request)) {
            $uow->recomputeSingleEntityChangeSet($metadata, $request);
        } else {
            $uow->computeChangeSet($metadata, $request);
        }
    }

    /**
     * Returns the media from the resource event.
     *
     * @param ResourceEventInterface $event
     *
     * @return MediaInterface
     */
    protected function getMediaFromResourceEvent(ResourceEventInterface $event): MediaInterface
    {
        $media = $event->getResource();

        if (!$media instanceof MediaInterface) {
            throw new UnexpectedTypeException($media, MediaInterface::class);
        }

        return $media;
    }

    /**
     * Returns the video filesystem adapter.
     *
     * @return Local
     */
    private function getVideoAdapter(): Local
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->videoFilesystem->getAdapter();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MediaEvents::INSERT => ['onInsert', 0],
            MediaEvents::UPDATE => ['onUpdate', 0],
            MediaEvents::DELETE => ['onDelete', 0],
        ];
    }
}
