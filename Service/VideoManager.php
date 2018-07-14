<?php

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use FFMpeg;
use League\Flysystem\Filesystem as Flysystem;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class VideoManager
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class VideoManager
{
    const FORMATS = ['webm', 'mp4', 'ogg'];

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
    private $videoDirectory;

    /**
     * @var string
     */
    private $watermark;

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
     * @param string                $videoDirectory
     * @param string                $watermark
     */
    public function __construct(
        Flysystem $mediaFilesystem,
        Flysystem $videoFilesystem,
        FFMpeg\FFMpeg $ffmpeg,
        FFMpeg\FFProbe $ffprobe,
        CacheManager $cacheManager,
        UrlGeneratorInterface $urlGenerator,
        string $videoDirectory,
        string $watermark = null
    ) {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->videoFilesystem = $videoFilesystem;
        $this->ffmpeg = $ffmpeg;
        $this->ffprobe = $ffprobe;
        $this->cacheManager = $cacheManager;
        $this->urlGenerator = $urlGenerator;
        $this->videoDirectory = $videoDirectory;
        $this->watermark = $watermark;

        $this->fs = new Filesystem();
    }

    /**
     * Returns the browser path.
     *
     * @param MediaInterface $media
     * @param                $format
     *
     * @return null|string
     */
    public function getBrowserPath(MediaInterface $media, $format)
    {
        $this->assertVideo($media);

        if (!in_array($format, static::FORMATS, true)) {
            $format = 'mp4';
        }

        if (null === $targetKey = $this->getTargetKey($media, $format)) {
            return null;
        }

        if ($this->videoFilesystem->has($targetKey)) {
            return '/'.$this->videoDirectory . '/' . $targetKey;
        }

        return $this->urlGenerator->generate(
            'ekyna_media_video',
            ['key' => $media->getPath(), '_format' => $format ? $format : 'mp4'],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * Converts the video.
     *
     * @param MediaInterface $media
     * @param string         $format
     *
     * @return string|null The absolute file path
     */
    public function convert(MediaInterface $media, $format)
    {
        $this->assertVideo($media);
        $this->assertFormat($format);

        if (null === $targetKey = $this->getTargetKey($media, $format)) {
            return null;
        }

        $targetPath = $this->getVideoAbsolutePath($targetKey, false);

        if ($this->videoFilesystem->has($targetKey)) {
            return $targetPath;
        }

        $this->checkDir(dirname($targetPath));

        switch ($format) {
            case 'webm' :
                $codec = new FFMpeg\Format\Video\WebM();
                break;
            case 'mp4' :
                $codec = new FFMpeg\Format\Video\X264();
                break;
            case 'ogg' :
                $codec = new FFMpeg\Format\Video\Ogg();
                break;
            default:
                throw new InvalidArgumentException("Unexpected video format.");
        }

        try {
            $sourcePath = $this->getMediaAbsolutePath($media->getPath());

            $video = $this->ffmpeg->open($sourcePath);

            $dimensions = $video
                ->getStreams()
                ->first()
                ->getDimensions();

            $realRatio = $dimensions->getRatio(false);
            $normRatio = $dimensions->getRatio();

            // Can't apply filters on webm with non standard aspect ratio
            if ('webm' == $format && $realRatio != $normRatio) {
                $this->fs->copy($sourcePath, $targetPath);

                return $targetPath;
            }

            if (1280 < $dimensions->getWidth()) {
                $dimensions = new FFMpeg\Coordinate\Dimension(1280, 1280 / $dimensions->getRatio(false));
                $video
                    ->filters()
                    ->resize($dimensions, FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_INSET)
                    ->synchronize();
            }

            // Watermark
            if (file_exists($this->watermark)) {
                $video
                    ->filters()
                    ->watermark($this->watermark, [
                        'position' => 'relative',
                        'bottom'   => 20,
                        'right'    => 20,
                    ]);
            }

            $codec->setKiloBitrate(1000);

            // Save Video
            $video->save($codec, $targetPath);
        } catch (FFMpeg\Exception\ExceptionInterface $e) {
            @unlink($targetPath);

            return null;
        }

        return $targetPath;
    }

    /**
     * Returns the video thumb's path.
     *
     * @param MediaInterface $media
     * @param string         $filter
     *
     * @return string|null
     */
    public function thumb(MediaInterface $media, $filter = 'video_alt')
    {
        $this->assertVideo($media);

        $sourceKey = $media->getPath();

        if (!$this->mediaFilesystem->has($sourceKey)) {
            return null;
        }

        $info = pathinfo($sourceKey);
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
            $second = $duration < 6 ? round($duration / 2, 1) : 3;

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
     * Returns the absolute path for the given file key.
     *
     * @param string $key
     * @param bool   $check
     *
     * @return string
     */
    private function getMediaAbsolutePath($key, $check = true)
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
     * @return string
     */
    private function getVideoAbsolutePath($key, $check = true)
    {
        if ($check && !$this->videoFilesystem->has($key)) {
            return null;
        }

        return $this->getVideoAdapter()->applyPathPrefix($key);
    }

    /**
     * Returns the filesystem adapter.
     *
     * @return \League\Flysystem\Adapter\Local
     */
    private function getMediaAdapter()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->mediaFilesystem->getAdapter();
    }

    /**
     * Returns the video filesystem adapter.
     *
     * @return \League\Flysystem\Adapter\Local
     */
    private function getVideoAdapter()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->videoFilesystem->getAdapter();
    }

    /**
     * Asserts that the media is of video type.
     *
     * @param MediaInterface $media
     */
    private function assertVideo(MediaInterface $media)
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
    private function assertFormat(string $format)
    {
        if (!in_array($format, static::FORMATS, true)) {
            throw new InvalidArgumentException("Expected format as 'mp4', 'webm' or 'ogg'.");
        }
    }

    /**
     * Builds and returns the target key.
     *
     * @param MediaInterface $media
     * @param                $format
     *
     * @return null|string
     */
    private function getTargetKey(MediaInterface $media, $format)
    {
        $sourceKey = $media->getPath();

        if (!$this->mediaFilesystem->has($sourceKey)) {
            return null;
        }

        $info = pathinfo($sourceKey);

        return $info['dirname'] . '/' . $info['filename'] . '.' . $format;
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
