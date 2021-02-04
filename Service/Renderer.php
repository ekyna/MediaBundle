<?php

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Class Renderer
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Renderer
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var VideoManager
     */
    private $videoManager;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var MediaRepository
     */
    private $repository;

    /**
     * @var TemplateWrapper
     */
    private $template;


    /**
     * Constructor.
     *
     * @param Environment     $twig
     * @param Generator       $generator
     * @param VideoManager    $videoManager
     * @param FilterManager   $filterManager
     * @param MediaRepository $repository
     * @param string          $template
     */
    public function __construct(
        Environment $twig,
        Generator $generator,
        VideoManager $videoManager,
        FilterManager $filterManager,
        MediaRepository $repository,
        $template = '@EkynaMedia/Media/element.html.twig'
    ) {
        $this->twig = $twig;
        $this->generator = $generator;
        $this->videoManager = $videoManager;
        $this->filterManager = $filterManager;
        $this->repository = $repository;
        $this->template = $template;
    }

    /**
     * Returns the generator.
     *
     * @return Generator
     */
    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    /**
     * Finds the media by its id and type.
     *
     * @param int|null $id
     * @param string   $type
     *
     * @return MediaInterface|null
     */
    public function findMedia(int $id = null, string $type = MediaTypes::IMAGE): ?MediaInterface
    {
        if (!$id) {
            return null;
        }

        if (null === $media = $this->repository->find($id)) {
            return null;
        }

        if ($media->getType() !== $type) {
            return null;
        }

        return $media;
    }

    /**
     * Renders the media.
     *
     * @param MediaInterface $media
     * @param array          $params
     *
     * @return string
     */
    public function renderMedia(MediaInterface $media, array $params = []): string
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
            case MediaTypes::SVG :
                return $this->renderSvg($media, $params);
        }

        return $this->renderFile($media, $params);
    }

    /**
     * Renders the video.
     *
     * @param MediaInterface $video
     * @param array          $params
     *
     * @return string
     */
    public function renderVideo(MediaInterface $video, array $params = []): string
    {
        if (!MediaTypes::isVideo($video)) {
            throw new \InvalidArgumentException('Expected media with "video" type.');
        }

        $params = array_replace_recursive([
            'responsive'   => true,
            'autoplay'     => true,
            'loop'         => false,
            'muted'        => false,
            'player'       => true,
            'alt_message'  => 'ekyna_media.player.video_not_supported',
            'aspect_ratio' => '16by9',
            'attr'         => [
                'id'     => 'media-video-' . $video->getId(),
                'height' => '100%',
                'width'  => '100%',
            ],
        ], $params);

        if ($params['autoplay']) {
            $params['attr']['autoplay'] = null;
            // https://developers.google.com/web/updates/2017/09/autoplay-policy-changes#best-practices
            $params['attr']['muted'] = null;
            // https://webkit.org/blog/6784/new-video-policies-for-ios/
            $params['attr']['playsinline'] = null;
            $params['attr']['preload'] = 'auto';
        } else {
            $params['attr']['preload'] = 'none';
        }
        if ($params['loop']) {
            $params['attr']['loop'] = null;
        }
        if ($params['muted']) {
            $params['attr']['muted'] = null;
        }
        if ($params['player']) {
            $params['attr']['controls'] = null;
            /*$params['attr']['class'] = 'video-js vjs-default-skin vjs-big-play-centered';
            $params['attr']['data-setup'] = [];
            if ($params['responsive']) {
                $params['attr']['data-setup']['fluid'] = true;
                $params['attr']['data-setup']['textTrackSettings'] = false;
            }*/
        }

        // Poster attribute
        if (null !== $poster = $this->videoManager->thumb($video, 'video_alt')) {
            $params['attr']['poster'] = $poster;
        }

        // Sources
        $videos = [];
        foreach (MediaFormats::getFormatsByType(MediaTypes::VIDEO) as $format) {
            $videos['video/' . $format] = $this->generator->generateFrontUrl($video, $format);
        }

        return $this->renderBlock('video', [
            'videos'       => $videos,
            'responsive'   => $params['responsive'],
            'aspect_ratio' => $params['aspect_ratio'],
            'alt_message'  => $params['alt_message'],
            'attr'         => $params['attr'],
        ]);
    }

    /**
     * Renders the flash swf.
     *
     * @param MediaInterface $flash
     * @param array          $params
     *
     * @return string
     */
    public function renderFlash(MediaInterface $flash, array $params = []): string
    {
        if (!MediaTypes::isFlash($flash)) {
            throw new \InvalidArgumentException('Expected media with "flash" type.');
        }

        $params = array_replace_recursive([
            //'responsive' => false,
            'attr' => [
                'id'     => 'media-flash-' . $flash->getId(),
                'class'  => 'swf-object',
                //'classid' => 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
                'height' => '100%',
                'width'  => '100%',
            ],
        ], $params);

        return $this->renderBlock('flash', [
            'src'  => $this->generator->generateFrontUrl($flash),
            'attr' => $params['attr'],
        ]);
    }

    /**
     * Renders the audio.
     *
     * @param MediaInterface $audio
     * @param array          $params
     *
     * @return string
     */
    public function renderAudio(MediaInterface $audio, array $params = []): string
    {
        if (!MediaTypes::isAudio($audio)) {
            throw new \InvalidArgumentException('Expected media with "audio" type.');
        }

        $params = array_replace_recursive([
            'alt_message' => 'ekyna_media.player.audio_not_supported',
            'attr'        => [
                'id' => 'media-audio-' . $audio->getId(),
            ],
        ], $params);

        return $this->renderBlock('audio', [
            'src'         => $this->generator->generateFrontUrl($audio),
            'mime_type'   => $this->generator->getMimeType($audio),
            'alt_message' => $params['alt_message'],
            'attr'        => $params['attr'],
        ]);
    }

    /**
     * Renders the image.
     *
     * @param MediaInterface $image
     * @param array          $params
     *
     * @return string
     */
    public function renderImage(MediaInterface $image, array $params = []): string
    {
        if (!MediaTypes::isImage($image)) {
            throw new \InvalidArgumentException('Expected media with "image" type.');
        }

        $params = array_replace_recursive([
            'filter' => 'media_front',
            'attr'   => [
                'id'  => 'media-image-' . $image->getId(),
                'alt' => $image->getTitle(),
            ],
        ], $params);

        $params['attr']['src'] = $this->generator->generateFrontUrl($image, $params['filter']);

        return $this->renderImg($params);
    }

    /**
     * Renders the svg.
     *
     * @param MediaInterface $svg
     * @param array          $params
     *
     * @return string
     */
    public function renderSvg(MediaInterface $svg, array $params = []): string
    {
        if (!MediaTypes::isSvg($svg)) {
            throw new \InvalidArgumentException('Expected media with "svg" type.');
        }

        $path = $this->generator->generateFrontUrl($svg);

        $params = array_replace_recursive([
            'attr' => [
                'id'    => 'media-svg-' . $svg->getId(),
                'title' => $svg->getTitle(),
            ],
        ], $params);

        $mode = isset($params['mode']) ? $params['mode'] : 'content';
        unset($params['mode']);

        if ($mode === 'object') {
            $params['attr']['data'] = $path;
            $params['attr']['type'] = 'image/svg+xml';

            return $this->renderBlock('object', [
                'attr'     => $params['attr'],
                'fallback' => $svg->getTitle(),
            ]);
        } elseif ($mode === 'image') {
            $params['attr']['src'] = $path;
            $params['attr']['alt'] = $svg->getTitle();

            return $this->renderBlock('image', [
                'attr' => $params['attr'],
            ]);
        }

        // By Content
        $doc = new \DOMDocument();
        $doc->loadXML($this->generator->getContent($svg));
        $nodes = $doc->getElementsByTagName('svg');
        if (1 == $nodes->length) {
            $node = $nodes->item(0);

            foreach ($params['attr'] as $key => $value) {
                $node->setAttribute($key, $value);
            }

            return $doc->saveHTML($node);
        }

        return '';
    }

    /**
     * Renders the file (link).
     *
     * @param MediaInterface $file
     * @param array          $params
     *
     * @return string
     */
    public function renderFile(MediaInterface $file, array $params = []): string
    {
        if (!(MediaTypes::isFile($file) || MediaTypes::isArchive($file))) {
            throw new \InvalidArgumentException('Expected media with "file" or "archive" type.');
        }

        $params = array_replace_recursive([
            'attr' => [
                'id'    => 'media-file-' . $file->getId(),
                'title' => $file->getTitle(),
            ],
        ], $params);

        $params['attr']['href'] = $this->generator->generateFrontUrl($file);

        return $this->renderBlock('file', [
            'name' => pathinfo($file->getPath(), PATHINFO_BASENAME),
            'attr' => $params['attr'],
        ]);
    }

    /**
     * Renders the video thumb.
     *
     * @param MediaInterface $video
     * @param array          $params
     *
     * @return string
     */
    public function renderVideoThumb(MediaInterface $video, array $params = []): string
    {
        if (!MediaTypes::isVideo($video)) {
            throw new \InvalidArgumentException('Expected media with "video" type.');
        }

        $params = array_replace_recursive([
            'filter' => 'video_alt',
            'attr'   => [
                'id'  => 'media-video-' . $video->getId() . '-thumb',
                'alt' => $video->getTitle(),
                'src' => null,
            ],
        ], $params);

        $params['attr']['src'] = $this->videoManager->thumb($video, $params['filter']);

        return $this->renderImg($params);
    }

    /**
     * Renders img element.
     *
     * @param array $params
     *
     * @return string
     */
    private function renderImg(array $params): string
    {
        $params = array_replace_recursive([
            'filter' => 'media_front',
            'attr'   => [
                'class' => 'img-responsive',
                'src'   => null,
            ],
        ], $params);

        if (!isset($params['attr']['src'])) {
            throw new \RuntimeException("The 'src' attribute must be set.");
        }

        if (!(isset($params['attr']['width']) && isset($params['attr']['height']))) {
            $filter = $this->filterManager->getFilterConfiguration()->get($params['filter']);
            if (array_key_exists('filters', $filter)) {
                // TODO better size resolution
                $width = $height = 0;
                foreach ($filter['filters'] as $cfg) {
                    if (array_key_exists('size', $cfg)) {
                        $width = $width >= $cfg['size'][0] ?: $cfg['size'][0];
                        $height = $height >= $cfg['size'][1] ?: $cfg['size'][1];
                    }
                }
                if ($width && $height) {
                    $params['attr']['width'] = $width;
                    $params['attr']['height'] = $height;
                }
            }
        }

        return $this->renderBlock('image', [
            'attr' => $params['attr'],
        ]);
    }

    /**
     * Renders the template block.
     *
     * @param $blockName
     * @param $blockVars
     *
     * @return string
     */
    private function renderBlock($blockName, $blockVars): string
    {
        if (!$this->template instanceof TemplateWrapper) {
            $this->template = $this->twig->load($this->template);
        }

        return $this->template->renderBlock($blockName, $blockVars);
    }
}
