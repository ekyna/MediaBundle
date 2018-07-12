<?php

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Imagine\Exception\RuntimeException as ImagineException;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Generator
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Generator
{
    const DEFAULT_THUMB = '/bundles/ekynamedia/img/file.jpg';
    const NONE_THUMB = '/bundles/ekynamedia/img/media-none.jpg';

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var VideoManager
     */
    private $videoManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $iconsSourcePath;

    /**
     * @var string
     */
    private $webRootDirectory;

    /**
     * @var string
     */
    private $thumbsDirectory;

    /**
     * @var Filesystem
     */
    private $fs;


    /**
     * Constructor.
     *
     * @param FilesystemInterface   $filesystem
     * @param ImagineInterface      $imagine
     * @param CacheManager          $cacheManager
     * @param VideoManager          $videoManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param string                $webRootDirectory
     * @param string                $thumbsDirectory
     */
    public function __construct(
        FilesystemInterface $filesystem,
        ImagineInterface $imagine,
        CacheManager $cacheManager,
        VideoManager $videoManager,
        UrlGeneratorInterface $urlGenerator,
        $webRootDirectory,
        $thumbsDirectory
    ) {
        $this->filesystem = $filesystem;
        $this->imagine = $imagine;
        $this->cacheManager = $cacheManager;
        $this->videoManager = $videoManager;
        $this->urlGenerator = $urlGenerator;

        $this->webRootDirectory = realpath($webRootDirectory);
        $this->thumbsDirectory = $thumbsDirectory;
        $this->iconsSourcePath = realpath(__DIR__ . '/../Resources/extensions');

        $this->fs = new Filesystem();
    }

    /**
     * Returns the media file content.
     *
     * @param MediaInterface $media
     *
     * @return string|null
     */
    public function getContent(MediaInterface $media)
    {
        if ($this->filesystem->has($media->getPath())) {
            return $this->filesystem->read($media->getPath()) ?: null;
        }

        return null;
    }

    /**
     * Generates a thumb for the given media.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function generateThumbUrl(MediaInterface $media)
    {
        $path = null;

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
    public function generateFrontUrl(MediaInterface $media, $format = 'media_front')
    {
        if ($this->isImagineFilterable($media)) {
            return $this->cacheManager->getBrowserPath($media->getPath(), $format ? $format : 'media_front');
        }

        if (MediaTypes::isVideo($media)) {
            return $this->videoManager->getBrowserPath($media, $format);
            /*return $this->urlGenerator->generate(
                'ekyna_media_video',
                ['key' => $media->getPath(), '_format' => $format ? $format : 'mp4'],
                UrlGeneratorInterface::ABSOLUTE_URL
            );*/
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
    public function generatePlayerUrl(MediaInterface $media)
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
     * @return null|string
     */
    private function generateVideoThumb(MediaInterface $media, $filter = 'video_thumb')
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
     * @return null|string
     */
    private function generateFileThumb(MediaInterface $media)
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
        } catch (ImagineException $e) {
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
    public function isImagineFilterable(MediaInterface $media)
    {
        return $media->getType() === MediaTypes::IMAGE
            && 0 < preg_match('~^image/(jpe?g|gif|png)$~', $this->getMimeType($media));
    }

    /**
     * Returns the media mime type.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getMimeType(MediaInterface $media)
    {
        return $this->filesystem->getMimetype($media->getPath());
    }

    /**
     * Returns the default thumb path.
     *
     * @return string
     */
    public function getDefaultThumb()
    {
        return self::DEFAULT_THUMB;
    }

    /**
     * Returns the none thumb path.
     *
     * @return string
     */
    public function getNoneThumb()
    {
        return self::NONE_THUMB;
    }

    /**
     * Creates the directory if it does not exists.
     *
     * @param $dir
     */
    private function checkDir($dir)
    {
        if (!$this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }
}
