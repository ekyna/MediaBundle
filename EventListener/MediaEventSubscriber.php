<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\EventListener;

use Ekyna\Bundle\MediaBundle\Message\ConvertVideo;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\Resource\Message\MessageQueueAwareTrait;
use Ekyna\Component\Resource\Persistence\PersistenceHelperInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;

use function pathinfo;

/**
 * Class MediaEventSubscriber
 * @package Ekyna\Bundle\MediaBundle\EventListener
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaEventSubscriber
{
    use MessageQueueAwareTrait;

    public function __construct(
        protected readonly PersistenceHelperInterface $persistenceHelper,
        protected readonly Filesystem $videoFilesystem,
    ) {
    }

    public function onInsert(ResourceEventInterface $event): void
    {
        $media = $this->getMediaFromResourceEvent($event);

        $this->sendConvertVideoMessages($media, false);
    }

    public function onUpdate(ResourceEventInterface $event): void
    {
        $media = $this->getMediaFromResourceEvent($event);

        if ($media->getType() !== MediaTypes::VIDEO) {
            return;
        }

        $changeSet = $this->persistenceHelper->getChangeSet($media, [
            'path',
            'size',
        ]);

        $override = false;
        if ($this->persistenceHelper->isChanged($media, 'path')) {
            $this->removeConvertedVideos($changeSet['path'][0]);
        } elseif ($this->persistenceHelper->isChanged($media, 'size')) {
            $override = true;
        } else {
            return;
        }

        $this->sendConvertVideoMessages($media, $override);
    }

    public function onDelete(ResourceEventInterface $event): void
    {
        $media = $this->getMediaFromResourceEvent($event);

        if ($media->getType() !== MediaTypes::VIDEO) {
            return;
        }

        if ($this->persistenceHelper->isChanged($media, 'path')) {
            $path = $this->persistenceHelper->getChangeSet($media, 'path')[0];
        } else {
            $path = $media->getPath();
        }

        $this->removeConvertedVideos($path);
    }

    protected function sendConvertVideoMessages(MediaInterface $media, bool $override): void
    {
        if (MediaTypes::VIDEO !== $media->getType()) {
            return;
        }

        foreach (MediaFormats::getFormatsByType(MediaTypes::VIDEO) as $format) {
            $this->messageQueue->addMessage(static function () use ($media, $format, $override) {
                return new ConvertVideo(
                    $media->getId(),
                    $media->getPath(),
                    $format,
                    $override
                );
            });
        }
    }

    /**
     * Removes the converted videos.
     */
    protected function removeConvertedVideos(string $path): void
    {
        $info = pathinfo($path);

        foreach (MediaFormats::getFormatsByType(MediaTypes::VIDEO) as $format) {
            $key = $info['dirname'] . '/' . $info['filename'] . '.' . $format;

            try {
                if (!$this->videoFilesystem->fileExists($key)) {
                    continue;
                }
            } catch (FilesystemException) {
                continue;
            }

            try {
                $this->videoFilesystem->delete($key);
            } catch (FilesystemException) {
            }
        }
    }

    /**
     * Returns the media from the resource event.
     */
    protected function getMediaFromResourceEvent(ResourceEventInterface $event): MediaInterface
    {
        $media = $event->getResource();

        if (!$media instanceof MediaInterface) {
            throw new UnexpectedTypeException($media, MediaInterface::class);
        }

        return $media;
    }
}
