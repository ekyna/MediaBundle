<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\FFMpeg\X264;
use Ekyna\Bundle\ResourceBundle\Service\Filesystem\FilesystemHelper;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Exception\ExceptionInterface as FFMpegException;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\Ogg;
use FFMpeg\Format\Video\WebM;
use League\Flysystem\Filesystem as Flysystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use LogicException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function array_replace;
use function dirname;
use function file_exists;
use function floor;
use function getimagesize;
use function in_array;
use function pathinfo;

/**
 * Class VideoManager
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoManager
{
    private array                     $config;
    private readonly Filesystem       $fs;
    private readonly FilesystemHelper $mediaHelper;
    private readonly FilesystemHelper $videoHelper;

    public function __construct(
        private readonly Flysystem $mediaFilesystem,
        private readonly Flysystem $videoFilesystem,
        private readonly FFMpeg $ffMpeg,
        private readonly FFProbe $ffProbe,
        private readonly CacheManager $cacheManager,
        private readonly UrlGeneratorInterface $urlGenerator,
        array   $config = []
    ) {
        $this->config = array_replace([
            'directory' => 'cache/video',
            'watermark' => null,
            'pending'   => null,
        ], $config);

        $this->fs = new Filesystem();
        $this->mediaHelper = new FilesystemHelper($this->mediaFilesystem);
        $this->videoHelper = new FilesystemHelper($this->videoFilesystem);
    }

    /**
     * Returns the browser path.
     */
    public function getBrowserPath(MediaInterface $media, string $format): ?string
    {
        $this->assertVideo($media);

        if (!in_array($format, MediaFormats::getFormatsByType(MediaTypes::VIDEO), true)) {
            $format = MediaFormats::MP4;
        }

        if (null === $targetKey = $this->getTargetKey($media->getPath(), $format)) {
            return null;
        }

        if ($this->videoHelper->fileExists($targetKey, false)) {
            return '/' . $this->config['directory'] . '/' . $targetKey;
        }

        return $this->urlGenerator->generate(
            'ekyna_media_video',
            ['key' => $media->getPath(), '_format' => $format ?: MediaFormats::MP4],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Returns the path of the converted video file for the given format.
     *
     * @return string The converted video absolute file path (that may not exist).
     */
    public function getConvertedPath(MediaInterface $media, string $format): string
    {
        $this->assertVideo($media);
        $this->assertFormat($format);

        $targetKey = $this->getTargetKey($media->getPath(), $format, false);

        return $this->getVideoAbsolutePath($targetKey, false);
    }

    /**
     * Returns the pending video absolute path.
     */
    public function getPendingVideoPath(string $format): ?string
    {
        if (empty($sourcePath = $this->config['pending'])) {
            return null;
        }

        $targetKey = pathinfo($sourcePath)['filename'] . '.' . $format;

        $targetPath = $this->getVideoAbsolutePath($targetKey, false);

        if ($this->videoHelper->fileExists($targetKey, false)) {
            // Conversion has been made.
            return $targetPath;
        }

        return $this->convert($sourcePath, $targetPath);
    }

    /**
     * Converts the video media to the given format.
     */
    public function convertVideo(MediaInterface $media, string $format, bool $override = false): string
    {
        $this->assertVideo($media);
        $this->assertFormat($format);

        $sourceKey = $media->getPath();

        if (null === $targetKey = $this->getTargetKey($sourceKey, $format)) {
            throw new LogicException('Video file not found');
        }

        $targetPath = $this->getVideoAbsolutePath($targetKey, false);

        if ($this->videoHelper->fileExists($targetKey, false)) {
            if ($override) {
                // Remove the file
                $this->videoFilesystem->delete($targetKey);
            } else {
                // Conversion has been made.
                return $targetPath;
            }
        }

        return $this->convert($this->getMediaAbsolutePath($sourceKey), $targetPath);
    }

    /**
     * Returns the video thumb's path.
     */
    public function thumb(MediaInterface $media, string $filter = 'video_alt'): ?string
    {
        $this->assertVideo($media);

        $sourceKey = $media->getPath();

        if (!$this->mediaHelper->fileExists($sourceKey, false)) {
            return null;
        }

        $info = pathinfo($sourceKey);
        $targetKey = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.jpg';

        if ($this->mediaHelper->fileExists($targetKey, false)) {
            return $this->cacheManager->getBrowserPath($targetKey, $filter);
        }

        $sourcePath = $this->getMediaAbsolutePath($sourceKey);
        $targetPath = $this->getMediaAbsolutePath($targetKey, false);

        $this->checkDir(dirname($targetPath));

        try {
            $video = $this->ffMpeg->open($sourcePath);

            $dimensions = $video
                ->getStreams()
                ->videos()
                ->first()
                ->getDimensions();

            if (1280 < $dimensions->getWidth()) {
                $dimensions = new Dimension(1280, 1280 / $dimensions->getRatio(false)->getValue());
                $video
                    ->filters()
                    ->resize($dimensions, ResizeFilter::RESIZEMODE_INSET)
                    ->synchronize();
            }

            $duration = $this->ffProbe->format($sourcePath)->get('duration');
            $second = $duration < 6 ? round($duration / 2, 1) : 3;

            // Save frame
            $video
                ->frame(TimeCode::fromSeconds($second))
                ->save($targetPath);
        } catch (FFMpegException) {
            return null;
        }

        return $this->cacheManager->getBrowserPath($targetKey, $filter);
    }

    /**
     * Converts the video key to the given format.
     */
    private function convert(string $sourcePath, string $targetPath): string
    {
        $this->checkDir(dirname($targetPath));

        $codec = match (pathinfo($targetPath)['extension']) {
            MediaFormats::WEBM => new WebM(),
            MediaFormats::MP4 => new X264(),
            MediaFormats::OGG => new Ogg(),
            default => throw new InvalidArgumentException('Unexpected video format.'),
        };

        $video = $this->ffMpeg->open($sourcePath);

        $dimensions = $video
            ->getStreams()
            ->videos()
            ->first()
            ->getDimensions();

        $realRatio = $dimensions->getRatio(false)->getValue();
        $normRatio = $dimensions->getRatio()->getValue();

        // Resize / use standard ratio
        if (720 < $dimensions->getWidth() || $realRatio !== $normRatio) {
            $width = min(720, $dimensions->getWidth());
            $resize = new Dimension($width, floor($width / $normRatio));
            $video
                ->filters()
                ->resize($resize, ResizeFilter::RESIZEMODE_INSET)
                ->synchronize();
        }

        // Watermark
        if ($this->config['watermark'] && file_exists($this->config['watermark'])) {
            $size = getimagesize($this->config['watermark']);
            $video
                ->filters()
                ->watermark($this->config['watermark'], [
                    'position' => 'absolute',
                    'x'        => $dimensions->getWidth() - 20 - $size[0],
                    'y'        => $dimensions->getHeight() - 20 - $size[1],
                ]);
        }

        // Save Video
        $video->save($codec, $targetPath);

        return $targetPath;
    }

    /**
     * Returns the absolute path for the given file key.
     */
    private function getMediaAbsolutePath(string $key, bool $check = true): ?string
    {
        if ($check && !$this->mediaHelper->fileExists($key, false)) {
            return null;
        }

        return $this->mediaHelper->getRealPath($key);
    }

    /**
     * Returns the absolute path for the given file key.
     */
    private function getVideoAbsolutePath(string $key, bool $check = true): ?string
    {
        if ($check && !$this->videoHelper->fileExists($key, false)) {
            return null;
        }

        return $this->videoHelper->getRealPath($key);
    }

    /**
     * Asserts that the media is of video type.
     */
    private function assertVideo(MediaInterface $media): void
    {
        if (!MediaTypes::isVideo($media)) {
            throw new InvalidArgumentException('Expected video media.');
        }
    }

    /**
     * Asserts that the format is supported.
     */
    private function assertFormat(string $format): void
    {
        if (!in_array($format, MediaFormats::getFormatsByType(MediaTypes::VIDEO), true)) {
            throw new InvalidArgumentException("Expected format as 'mp4', 'webm' or 'ogg'.");
        }
    }

    /**
     * Builds and returns the target key.
     */
    private function getTargetKey(string $sourceKey, string $format, bool $check = true): ?string
    {
        if ($check && !$this->mediaHelper->fileExists($sourceKey, false)) {
            return null;
        }

        $info = pathinfo($sourceKey);

        return $info['dirname'] . '/' . $info['filename'] . '.' . $format;
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
