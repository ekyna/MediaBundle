<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PlayerExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PlayerExtension extends \Twig_Extension
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

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
     * @param FilesystemInterface   $filesystem
     * @param UrlGeneratorInterface $urlGenerator
     * @param FilterManager         $filterManager
     */
    public function __construct(
        FilesystemInterface   $filesystem,
        UrlGeneratorInterface $urlGenerator,
        FilterManager         $filterManager
    ) {
        $this->filesystem       = $filesystem;
        $this->urlGenerator     = $urlGenerator;
        $this->filterManager    = $filterManager;
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->elementTemplate = $twig->loadTemplate('EkynaMediaBundle:Media:element.html.twig');
    }

    /**
     * {@inheritDoc}
     */
    /*public function getGlobals()
    {
        return array();
    }*/

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('media',       array($this, 'renderMedia'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('media_video', array($this, 'renderVideo'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('media_flash', array($this, 'renderFlash'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('media_audio', array($this, 'renderAudio'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('media_image', array($this, 'renderImage'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('media_file',  array($this, 'renderFile'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders the media.
     *
     * @param MediaInterface $media
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderMedia(MediaInterface $media, array $params = array())
    {
        switch ($media->getType()) {
            case MediaTypes::VIDEO :
                return $this->renderVideo($media, $params);
            case MediaTypes::FLASH :
                return $this->renderVideo($media, $params);
            case MediaTypes::AUDIO :
                return $this->renderVideo($media, $params);
            case MediaTypes::IMAGE :
                return $this->renderVideo($media, $params);
            default:
                return $this->renderFile($media, $params);
        }
    }

    /**
     * Renders the video.
     *
     * @param MediaInterface $video
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderVideo(MediaInterface $video, array $params = array())
    {
        if ($video->getType() !== MediaTypes::VIDEO) {
            throw new \InvalidArgumentException('Expected media with "video" type.');
        }

        $params = array_merge(array(
            'responsive'   => true,
            'aspect_ratio' => '16by9',
            'attr'         => array(
                'id'     => 'media-video-' . $video->getId(),
                'class'  => 'video-js vjs-default-skin vjs-big-play-centered',
                'height' => '480',
                'width'  => '720',
            ),
        ), $params);

        return $this->elementTemplate->renderBlock('video', array(
            'responsive'   => $params['responsive'],
            'aspect_ratio' => $params['aspect_ratio'],
            'src'          => $this->getDownloadUrl($video),
            'mime_type'    => $this->getMimeType($video),
            'attr'         => $params['attr'],
        ));
    }

    /**
     * Renders the flash swf.
     *
     * @param MediaInterface $flash
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderFlash(MediaInterface $flash, array $params = array())
    {
        if ($flash->getType() !== MediaTypes::FLASH) {
            throw new \InvalidArgumentException('Expected media with "flash" type.');
        }

        $params = array_merge(array(
            //'responsive' => $params['responsive'],
            'attr'       => array(
                'id'     => 'media-flash-' . $flash->getId(),
                'class'  => 'swf-object',
                //'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
                'height' => '480',
                'width'  => '720',
            ),
        ), $params);

        return $this->elementTemplate->renderBlock('flash', array(
            'src'  => $this->getDownloadUrl($flash),
            'attr' => $params['attr'],
        ));
    }

    /**
     * Renders the audio.
     *
     * @param MediaInterface $audio
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderAudio(MediaInterface $audio, array $params = array())
    {
        if ($audio->getType() !== MediaTypes::AUDIO) {
            throw new \InvalidArgumentException('Expected media with "audio" type.');
        }

        $params = array_merge(array(
            'attr' => array(
                'id' => 'media-audio-' . $audio->getId(),
            ),
        ), $params);

        return $this->elementTemplate->renderBlock('audio', array(
            'src'  => $this->getDownloadUrl($audio),
            'attr' => $params['attr'],
        ));
    }

    /**
     * Renders the image.
     *
     * @param MediaInterface $image
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderImage(MediaInterface $image, array $params = array())
    {
        if ($image->getType() !== MediaTypes::IMAGE) {
            throw new \InvalidArgumentException('Expected media with "image" type.');
        }

        $params = array_merge(array(
            'filter' => 'media_front',
            'attr' => array(
                'id' => 'media-image-' . $image->getId(),
            ),
        ), $params);

        if (!(array_key_exists('width', $params) && array_key_exists('height', $params))) {
            $filter = $this->filterManager->getFilterConfiguration()->get($params['filter']);
            if (array_key_exists('filters', $filter)) {
                $width = $height = 0;
                foreach ($filter['filters'] as $cfg) {
                    if (array_key_exists('size', $cfg)) {
                        if (array_key_exists('width', $cfg['size']) && $width < $cfg['size']['width']) {
                            $width = $cfg['size']['width'];
                        }
                        if (array_key_exists('height', $cfg['size']) && $width < $cfg['size']['height']) {
                            $width = $cfg['size']['height'];
                        }
                    }
                }
                $params = array_merge(array(
                    'attr' => array(
                        'width'  => $width,
                        'height' => $height,
                    ),
                ), $params);
            }
        }

        return $this->elementTemplate->renderBlock('image', array(
            'filter' => $params['filter'],
            'path'   => $image->getPath(),
            'alt'    => $image->getTitle(),
            'attr'   => $params['attr'],
        ));
    }

    /**
     * Renders the file (link).
     *
     * @param MediaInterface $file
     * @param array          $params
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderFile(MediaInterface $file, array $params = array())
    {
        if (in_array($file->getType(), array(MediaTypes::FILE, MediaTypes::ARCHIVE))) {
            throw new \InvalidArgumentException('Expected media with "file" or "archive" type.');
        }

        $params = array_replace(array(
            'attr' => array(
                'id' => 'image-' . $file->getId(),
            ),
        ), $params);

        return $this->elementTemplate->renderBlock('image', array(
            'attr'   => $params['attr'],
            'href'   => $this->getDownloadUrl($file),
            'title'  => $file->getTitle(),
        ));
    }

    /**
     * Returns the media download url.
     *
     * @param MediaInterface $media
     * @return string
     */
    private function getDownloadUrl(MediaInterface $media)
    {
        return $this->urlGenerator->generate('ekyna_media_download', array('key' => $media->getPath()));
    }

    /**
     * Returns the media mime type.
     *
     * @param MediaInterface $media
     * @return string
     */
    private function getMimeType(MediaInterface $media)
    {
        return $this->filesystem->getMimetype($media->getPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_player';
    }
}
