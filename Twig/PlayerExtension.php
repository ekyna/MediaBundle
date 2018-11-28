<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Service\Renderer;

/**
 * Class PlayerExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PlayerExtension extends \Twig_Extension
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Renderer
     */
    private $renderer;


    /**
     * Constructor.
     *
     * @param Generator $generator
     * @param Renderer  $renderer
     */
    public function __construct(Generator $generator, Renderer $renderer)
    {
        $this->generator = $generator;
        $this->renderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('media_image',   [MediaTypes::class, 'isImage']),
            new \Twig_SimpleTest('media_svg',     [MediaTypes::class, 'isSvg']),
            new \Twig_SimpleTest('media_flash',   [MediaTypes::class, 'isFlash']),
            new \Twig_SimpleTest('media_video',   [MediaTypes::class, 'isVideo']),
            new \Twig_SimpleTest('media_audio',   [MediaTypes::class, 'isAudio']),
            new \Twig_SimpleTest('media_file',    [MediaTypes::class, 'isFile']),
            new \Twig_SimpleTest('media_archive', [MediaTypes::class, 'isArchive']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('media_url',   [$this, 'getMediaUrl']),
            new \Twig_SimpleFilter('media',       [$this, 'renderMedia'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_video', [$this, 'renderVideo'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_flash', [$this, 'renderFlash'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_audio', [$this, 'renderAudio'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_image', [$this, 'renderImage'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_svg',   [$this, 'renderSvg'],   ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_file',  [$this, 'renderFile'],  ['is_safe' => ['html']]),
        ];
    }

    /**
     * Returns the media default url.
     *
     * @param MediaInterface $media
     * @param string         $filter
     *
     * @return string
     */
    public function getMediaUrl(MediaInterface $media, $filter = 'media_front')
    {
        return $this->generator->generateFrontUrl($media, $filter);
    }

    /**
     * Renders the media.
     *
     * @param MediaInterface $media
     * @param array          $params
     *
     * @return string
     */
    public function renderMedia(MediaInterface $media, array $params = [])
    {
        return $this->renderer->renderMedia($media, $params);
    }

    /**
     * Renders the video.
     *
     * @param MediaInterface $video
     * @param array          $params
     *
     * @return string
     */
    public function renderVideo(MediaInterface $video, array $params = [])
    {
        return $this->renderer->renderVideo($video, $params);
    }

    /**
     * Renders the flash swf.
     *
     * @param MediaInterface $flash
     * @param array          $params
     *
     * @return string
     */
    public function renderFlash(MediaInterface $flash, array $params = [])
    {
        return $this->renderer->renderFlash($flash, $params);
    }

    /**
     * Renders the audio.
     *
     * @param MediaInterface $audio
     * @param array          $params
     *
     * @return string
     */
    public function renderAudio(MediaInterface $audio, array $params = [])
    {
        return $this->renderer->renderAudio($audio, $params);
    }

    /**
     * Renders the image.
     *
     * @param MediaInterface $image
     * @param array          $params
     *
     * @return string
     */
    public function renderImage(MediaInterface $image, array $params = [])
    {
        return $this->renderer->renderImage($image, $params);
    }

    /**
     * Renders the svg.
     *
     * @param MediaInterface $svg
     * @param array          $params
     *
     * @return string
     */
    public function renderSvg(MediaInterface $svg, array $params = [])
    {
        return $this->renderer->renderSvg($svg, $params);
    }

    /**
     * Renders the file (link).
     *
     * @param MediaInterface $file
     * @param array          $params
     *
     * @return string
     */
    public function renderFile(MediaInterface $file, array $params = [])
    {
        return $this->renderer->renderFile($file, $params);
    }
}
