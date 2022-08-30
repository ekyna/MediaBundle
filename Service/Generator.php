<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Imagine\Exception\RuntimeException as ImagineException;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToRetrieveMetadata;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function preg_match;

/**
 * Class Generator
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Generator
{
    public const DEFAULT_THUMB = '/bundles/ekynamedia/img/file.jpg';
    public const NONE_THUMB    = '/bundles/ekynamedia/img/media-none.jpg';

    private readonly string     $webRootDirectory;
    private readonly string     $thumbsDirectory;
    private readonly string     $iconsSourcePath;
    private readonly Filesystem $fs;

    public function __construct(
        private readonly FilesystemOperator    $filesystem,
        private readonly ImagineInterface      $imagine,
        private readonly CacheManager          $cacheManager,
        private readonly VideoManager          $videoManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        string                                 $webRootDirectory,
        string                                 $thumbsDirectory
    ) {
        $this->webRootDirectory = realpath($webRootDirectory);
        $this->thumbsDirectory = $thumbsDirectory;
        $this->iconsSourcePath = realpath(__DIR__ . '/../Resources/extensions');

        $this->fs = new Filesystem();
    }

    /**
     * Returns the media file content.
     *
     * @param MediaInterface $media
     * @param bool           $stream
     *
     * @return string|resource|null
     */
    public function getContent(MediaInterface $media, bool $stream = false)
    {
        try {
            $this->filesystem->fileExists($media->getPath());
        } catch (FilesystemException $exception) {
            return null;
        }

        try {
            if ($stream) {
                return $this->filesystem->readStream($media->getPath());
            }

            return $this->filesystem->read($media->getPath());
        } catch (FilesystemException $exception) {
            return null;
        }
    }

    /**
     * Generates a thumb for the given media.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function generateThumbUrl(MediaInterface $media): string
    {
        if ($this->isImagineFilterable($media)) {
            $path = $this->cacheManager->getBrowserPath($media->getPath(), 'media_thumb');
        } elseif (MediaTypes::isVideo($media)) {
            $path = $this->generateVideoThumb($media);
        } else {
            $path = $this->generateFileThumb($media);
        }

        if (null === $path) {
            $path = '/bundles/ekynamedia/img/file.jpg';
        }

        return $path;
    }

    /**
     * Generates the default front url.
     *
     * @param MediaInterface $media
     * @param string         $format : imagine filter for images, extension for videos
     *
     * @return string
     */
    public function generateFrontUrl(MediaInterface $media, string $format = 'media_front'): string
    {
        if ($this->isImagineFilterable($media)) {
            return $this->cacheManager->getBrowserPath($media->getPath(), $format ?: 'media_front');
        }

        if (MediaTypes::isVideo($media)) {
            return $this->videoManager->getBrowserPath($media, $format);
        }

        return $this->urlGenerator->generate(
            'ekyna_media_download',
            ['key' => $media->getPath()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Generates the player url.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function generatePlayerUrl(MediaInterface $media): string
    {
        if (in_array($media->getType(), [MediaTypes::VIDEO, MediaTypes::AUDIO, MediaTypes::FLASH])) {
            return $this->urlGenerator->generate(
                'ekyna_media_player',
                ['key' => $media->getPath()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->generateFrontUrl($media);
    }

    /**
     * Generates thumb for video elements.
     *
     * @param MediaInterface $media
     * @param string         $filter
     *
     * @return string|null
     */
    private function generateVideoThumb(MediaInterface $media, string $filter = 'video_thumb'): ?string
    {
        if (null !== $path = $this->videoManager->thumb($media, $filter)) {
            return $path;
        }

        return $this->generateFileThumb($media);
    }

    /**
     * Generates thumb for non-image elements.
     *
     * @param MediaInterface $media
     *
     * @return string|null
     */
    private function generateFileThumb(MediaInterface $media): ?string
    {
        $extension = $media->guessExtension();
        $thumbPath = sprintf('/%s/%s.jpg', $this->thumbsDirectory, $extension);
        $destination = $this->webRootDirectory . $thumbPath;

        if (file_exists($destination)) {
            return $thumbPath;
        }

        $backgroundColor = MediaTypes::getColor($media->getType());

        $iconPath = sprintf('%s/%s.png', $this->iconsSourcePath, $extension);
        if (!file_exists($iconPath)) {
            $iconPath = $this->iconsSourcePath . '/default.png';
        }

        $this->checkDir(dirname($destination));
        try {
            $palette = new RGB();
            $thumb = $this->imagine->create(new Box(120, 90), $palette->color($backgroundColor));

            $icon = $this->imagine->open($iconPath);
            $iconSize = $icon->getSize();
            $start = new Point(120 / 2 - $iconSize->getWidth() / 2, 90 / 2 - $iconSize->getHeight() / 2);

            $thumb->paste($icon, $start);
            $thumb->save($destination);
        } catch (ImagineException) {
            // Image thumb generation failed
            return null;
        }

        return $thumbPath;
    }

    /**
     * Returns whether a imagine filter can be applied to the media on not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public function isImagineFilterable(MediaInterface $media): bool
    {
        try {
            $mime = $this->getMimeType($media);
        } catch (UnableToRetrieveMetadata) {
            return false;
        }

        return $media->getType() === MediaTypes::IMAGE
            && 0 < preg_match('~^image/(jpe?g|gif|png)$~', $mime);
    }

    /**
     * Returns the media mime type.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getMimeType(MediaInterface $media): string
    {
        return $this->filesystem->mimeType($media->getPath());
    }

    /**
     * Returns the default thumb path.
     *
     * @return string
     */
    public function getDefaultThumb(): string
    {
        return self::DEFAULT_THUMB;
    }

    /**
     * Returns the none thumb path.
     *
     * @return string
     */
    public function getNoneThumb(): string
    {
        return self::NONE_THUMB;
    }

    /**
     * Creates the directory if it does not exist.
     */
    private function checkDir(string $dir): void
    {
        if (!$this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }
}
