<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;

/**
 * Class PlayerExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PlayerExtension extends \Twig_Extension
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var \Twig_Template
     */
    private $elementTemplate;


    /**
     * Constructor.
     *
     * @param Generator     $generator
     * @param FilterManager $filterManager
     */
    public function __construct(Generator $generator, FilterManager $filterManager)
    {
        $this->generator     = $generator;
        $this->filterManager = $filterManager;
    }

    /**
     * @inheritdoc
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->elementTemplate = $twig->loadTemplate('EkynaMediaBundle:Media:element.html.twig');
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
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderMedia(MediaInterface $media, array $params = [])
    {
        switch ($media->getType()) {
            case MediaTypes::VIDEO :
                return $this->renderVideo($media, $params);
            case MediaTypes::FLASH :
                return $this->renderFlash($media, $params);
            case MediaTypes::AUDIO :
                return $this->renderAudio($media, $params);
            case MediaTypes::IMAGE :
                return $this->renderImage($media, $params);
        }
        return $this->renderFile($media, $params);
    }

    /**
     * Renders the video.
     *
     * @param MediaInterface $video
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderVideo(MediaInterface $video, array $params = [])
    {
        if ($video->getType() !== MediaTypes::VIDEO) {
            throw new \InvalidArgumentException('Expected media with "video" type.');
        }

        $params = array_merge([
            'responsive'   => false,
            'aspect_ratio' => '16by9',
            'attr'         => [
                'id'     => 'media-video-' . $video->getId(),
                'class'  => 'video-js vjs-default-skin vjs-big-play-centered',
                'height' => '100%',
                'width'  => '100%',
            ],
        ], $params);

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->elementTemplate->renderBlock('video', [
            'responsive'   => $params['responsive'],
            'aspect_ratio' => $params['aspect_ratio'],
            'src'          => $this->generator->generateFrontUrl($video),
            'mime_type'    => $this->generator->getMimeType($video),
            'attr'         => $params['attr'],
        ]);
    }

    /**
     * Renders the flash swf.
     *
     * @param MediaInterface $flash
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderFlash(MediaInterface $flash, array $params = [])
    {
        if ($flash->getType() !== MediaTypes::FLASH) {
            throw new \InvalidArgumentException('Expected media with "flash" type.');
        }

        $params = array_merge([
            //'responsive' => false,
            'attr'       => [
                'id'     => 'media-flash-' . $flash->getId(),
                'class'  => 'swf-object',
                //'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
                'height' => '100%',
                'width'  => '100%',
            ],
        ], $params);

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->elementTemplate->renderBlock('flash', [
            'src'  => $this->generator->generateFrontUrl($flash),
            'attr' => $params['attr'],
        ]);
    }

    /**
     * Renders the audio.
     *
     * @param MediaInterface $audio
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderAudio(MediaInterface $audio, array $params = [])
    {
        if ($audio->getType() !== MediaTypes::AUDIO) {
            throw new \InvalidArgumentException('Expected media with "audio" type.');
        }

        $params = array_merge([
            'attr' => [
                'id' => 'media-audio-' . $audio->getId(),
            ],
        ], $params);

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->elementTemplate->renderBlock('audio', [
            'src'  => $this->generator->generateFrontUrl($audio),
            'attr' => $params['attr'],
        ]);
    }

    /**
     * Renders the image.
     *
     * @param MediaInterface $image
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderImage(MediaInterface $image, array $params = [])
    {
        if ($image->getType() !== MediaTypes::IMAGE) {
            throw new \InvalidArgumentException('Expected media with "image" type.');
        }

        $params = array_merge([
            'filter' => 'media_front',
            'attr' => [
                'id'  => 'media-image-' . $image->getId(),
                'alt' => $image->getTitle(),
            ],
        ], $params);

        if (!(isset($params['attr']['width']) && isset($params['attr']['height']))) {
            $filter = $this->filterManager->getFilterConfiguration()->get($params['filter']);
            if (array_key_exists('filters', $filter)) {
                // TODO better size resolution
                $width = $height = 0;
                foreach ($filter['filters'] as $cfg) {
                    if (array_key_exists('size', $cfg)) {
                        $width  = $width  >= $cfg['size'][0] ?: $cfg['size'][0];
                        $height = $height >= $cfg['size'][1] ?: $cfg['size'][1];
                    }
                }
                if ($width && $height) {
                    $params['attr']['width']  = $width;
                    $params['attr']['height'] = $height;
                }
            }
        }

        $path = $this->generator->generateFrontUrl($image, $params['filter']);

        // TODO render svg (use wkHtmlToImage ?)
        /*if (!$this->generator->isImagineFilterable($image)) {
            $params['attr']['type'] = $this->generator->getMimeType($image);
            $params['attr']['data'] = $path;

            return $this->elementTemplate->renderBlock('svg', [
                'attr'     => $params['attr'],
                'fallback' => $image->getTitle(),
            ]);
        }*/

        $params['attr']['src'] = $path;

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->elementTemplate->renderBlock('image', [
            'attr' => $params['attr'],
        ]);
    }

    /**
     * Renders the file (link).
     *
     * @param MediaInterface $file
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderFile(MediaInterface $file, array $params = [])
    {
        if (in_array($file->getType(), [MediaTypes::FILE, MediaTypes::ARCHIVE])) {
            throw new \InvalidArgumentException('Expected media with "file" or "archive" type.');
        }

        $params = array_replace([
            'attr' => [
                'id'    => 'media-file-' . $file->getId(),
                'title' => $file->getTitle(),
            ],
        ], $params);

        $params['attr']['href'] = $this->generator->generateFrontUrl($file);

        /** @noinspection PhpInternalEntityUsedInspection */
        return $this->elementTemplate->renderBlock('file', [
            'name' => pathinfo($file->getPath(), PATHINFO_BASENAME),
            'attr' => $params['attr'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ekyna_media_player';
    }
}
