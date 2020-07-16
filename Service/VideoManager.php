<?php

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\FFMpeg\X264;
use FFMpeg;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as Flysystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use LogicException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class VideoManager
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoManager
{
    /**
     * @var Flysystem
     */
    private $mediaFilesystem;

    /**
     * @var Flysystem
     */
    private $videoFilesystem;

    /**
     * @var FFMpeg\FFMpeg
     */
    private $ffmpeg;

    /**
     * @var FFMpeg\FFProbe
     */
    private $ffprobe;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $config;

    /**
     * @var Filesystem
     */
    private $fs;


    /**
     * Constructor.
     *
     * @param Flysystem             $mediaFilesystem
     * @param Flysystem             $videoFilesystem
     * @param FFMpeg\FFMpeg         $ffmpeg
     * @param FFMpeg\FFProbe        $ffprobe
     * @param CacheManager          $cacheManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param array                 $config
     */
    public function __construct(
        Flysystem $mediaFilesystem,
        Flysystem $videoFilesystem,
        FFMpeg\FFMpeg $ffmpeg,
        FFMpeg\FFProbe $ffprobe,
        CacheManager $cacheManager,
        UrlGeneratorInterface $urlGenerator,
        array $config = []
    ) {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->videoFilesystem = $videoFilesystem;
        $this->ffmpeg          = $ffmpeg;
        $this->ffprobe         = $ffprobe;
        $this->cacheManager    = $cacheManager;
        $this->urlGenerator    = $urlGenerator;
        $this->config          = array_replace([
            'directory' => 'cache/video',
            'watermark' => null,
            'pending'   => null,
        ], $config);

        $this->fs = new Filesystem();
    }

    /**
     * Returns the browser path.
     *
     * @param MediaInterface $media
     * @param string         $format
     *
     * @return null|string
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

        if ($this->videoFilesystem->has($targetKey)) {
            return '/' . $this->config['directory'] . '/' . $targetKey;
        }

        return $this->urlGenerator->generate(
            'ekyna_media_video',
            ['key' => $media->getPath(), '_format' => $format ? $format : MediaFormats::MP4],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Returns the path of the converted video file for the given format.
     *
     * @param MediaInterface $media  The video media
     * @param string         $format The video format
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
     * Converts the video media to the given format.
     *
     * @param MediaInterface $media
     * @param string         $format
     * @param bool           $override
     *
     * @return string
     */
    public function convertVideo(MediaInterface $media, string $format, bool $override = false): string
    {
        $this->assertVideo($media);
        $this->assertFormat($format);

        $sourceKey = $media->getPath();

        if (null === $targetKey = $this->getTargetKey($sourceKey, $format)) {
            throw new LogicException("Video file not found");
        }

        $targetPath = $this->getVideoAbsolutePath($targetKey, false);

        if ($this->videoFilesystem->has($targetKey)) {
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
     * Returns the pending video absolute path.
     *
     * @param string $format
     *
     * @return string|null
     */
    public function getPendingVideoPath(string $format): ?string
    {
        if (empty($sourcePath = $this->config['pending'])) {
            return null;
        }

        $targetKey = pathinfo($sourcePath)['filename'] . '.' . $format;

        $targetPath = $this->getVideoAbsolutePath($targetKey, false);

        if ($this->videoFilesystem->has($targetKey)) {
            // Conversion has been made.
            return $targetPath;
        }

        return $this->convert($sourcePath, $targetPath);
    }

    /**
     * Returns the video thumb's path.
     *
     * @param MediaInterface $media
     * @param string         $filter
     *
     * @return string|null
     */
    public function thumb(MediaInterface $media, string $filter = 'video_alt'): ?string
    {
        $this->assertVideo($media);

        $sourceKey = $media->getPath();

        if (!$this->mediaFilesystem->has($sourceKey)) {
            return null;
        }

        $info      = pathinfo($sourceKey);
        $targetKey = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.jpg';

        if ($this->mediaFilesystem->has($targetKey)) {
            return $this->cacheManager->getBrowserPath($targetKey, $filter);
        }

        $sourcePath = $this->getMediaAbsolutePath($sourceKey);
        $targetPath = $this->getMediaAbsolutePath($targetKey, false);

        $this->checkDir(dirname($targetPath));

        try {
            $video = $this->ffmpeg->open($sourcePath);

            $dimensions = $video
                ->getStreams()
                ->first()
                ->getDimensions();

            if (1280 < $dimensions->getWidth()) {
                $dimensions = new FFMpeg\Coordinate\Dimension(1280, 1280 / $dimensions->getRatio(false));
                $video
                    ->filters()
                    ->resize($dimensions, FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_INSET)
                    ->synchronize();
            }

            $duration = $this->ffprobe->format($sourcePath)->get('duration');
            $second   = $duration < 6 ? round($duration / 2, 1) : 3;

            // Save frame
            $video
                ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($second))
                ->save($targetPath);

        } catch (FFMpeg\Exception\ExceptionInterface $e) {
            return null;
        }

        return $this->cacheManager->getBrowserPath($targetKey, $filter);
    }

    /**
     * Converts the video key to the given format.
     *
     * @param string $sourcePath
     * @param string $targetPath
     *
     * @return string
     */
    private function convert(string $sourcePath, string $targetPath): string
    {
        $this->checkDir(dirname($targetPath));

        switch (pathinfo($targetPath)['extension']) {
            case MediaFormats::WEBM :
                $codec = new FFMpeg\Format\Video\WebM();
                break;
            case MediaFormats::MP4 :
                $codec = new X264();
                break;
            case MediaFormats::OGG :
                $codec = new FFMpeg\Format\Video\Ogg();
                break;
            default:
                throw new InvalidArgumentException("Unexpected video format.");
        }

        $video = $this->ffmpeg->open($sourcePath);

        $dimensions = $video
            ->getStreams()
            ->first()
            ->getDimensions();

        $realRatio = $dimensions->getRatio(false);
        $normRatio = $dimensions->getRatio();

        // Resize / use standard ratio
        if (720 < $dimensions->getWidth() || $realRatio->getValue() !== $normRatio->getValue()) {
            $width  = min(720, $dimensions->getWidth());
            $resize = new FFMpeg\Coordinate\Dimension($width, floor($width / $dimensions->getRatio()->getValue()));
            $video
                ->filters()
                ->resize($resize, FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_INSET)
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
     *
     * @param string $key
     * @param bool   $check
     *
     * @return string|null
     */
    private function getMediaAbsolutePath(string $key, bool $check = true): ?string
    {
        if ($check && !$this->mediaFilesystem->has($key)) {
            return null;
        }

        return $this->getMediaAdapter()->applyPathPrefix($key);
    }

    /**
     * Returns the absolute path for the given file key.
     *
     * @param string $key
     * @param bool   $check
     *
     * @return string|null
     */
    private function getVideoAbsolutePath(string $key, bool $check = true): ?string
    {
        if ($check && !$this->videoFilesystem->has($key)) {
            return null;
        }

        return $this->getVideoAdapter()->applyPathPrefix($key);
    }

    /**
     * Returns the filesystem adapter.
     *
     * @return Local
     */
    private function getMediaAdapter(): Local
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->mediaFilesystem->getAdapter();
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
     * Asserts that the media is of video type.
     *
     * @param MediaInterface $media
     */
    private function assertVideo(MediaInterface $media): void
    {
        if (!MediaTypes::isVideo($media)) {
            throw new InvalidArgumentException("Expected video media.");
        }
    }

    /**
     * Asserts that the format is supported.
     *
     * @param string $format
     */
    private function assertFormat(string $format): void
    {
        if (!in_array($format, MediaFormats::getFormatsByType(MediaTypes::VIDEO), true)) {
            throw new InvalidArgumentException("Expected format as 'mp4', 'webm' or 'ogg'.");
        }
    }

    /**
     * Builds and returns the target key.
     *
     * @param string $sourceKey
     * @param string $format
     * @param bool   $check
     *
     * @return string|null
     */
    private function getTargetKey(string $sourceKey, string $format, bool $check = true): ?string
    {
        if ($check && !$this->mediaFilesystem->has($sourceKey)) {
            return null;
        }

        $info = pathinfo($sourceKey);

        return $info['dirname'] . '/' . $info['filename'] . '.' . $format;
    }

    /**
     * Creates the directory if it does not exists.
     *
     * @param string $dir
     */
    private function checkDir(string $dir): void
    {
        if (!$this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }
}
