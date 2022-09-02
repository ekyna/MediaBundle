<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service;

use DOMDocument;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use InvalidArgumentException;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Class Renderer
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaRenderer
{
    private ?TemplateWrapper $template = null;

    public function __construct(
        private readonly Environment              $twig,
        private readonly Generator                $generator,
        private readonly VideoManager             $videoManager,
        private readonly FilterManager            $filterManager,
        private readonly MediaRepositoryInterface $repository,
        private readonly TranslatorInterface      $translator,
        private readonly string                   $templatePath = '@EkynaMedia/Media/element.html.twig'
    ) {
    }

    /**
     * Returns the generator.
     */
    public function getGenerator(): Generator
    {
        return $this->generator;
    }

    /**
     * Finds the media by its id and type.
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
     */
    public function renderMedia(MediaInterface $media, array $params = []): string
    {
        return match ($media->getType()) {
            MediaTypes::VIDEO => $this->renderVideo($media, $params),
            MediaTypes::FLASH => $this->renderFlash($media, $params),
            MediaTypes::AUDIO => $this->renderAudio($media, $params),
            MediaTypes::IMAGE => $this->renderImage($media, $params),
            MediaTypes::SVG   => $this->renderSvg($media, $params),
            default           => $this->renderFile($media, $params),
        };
    }

    /**
     * Renders the video.
     */
    public function renderVideo(MediaInterface $video, array $params = []): string
    {
        if (!MediaTypes::isVideo($video)) {
            throw new InvalidArgumentException('Expected media with "video" type.');
        }

        $params = array_replace_recursive([
            'responsive'   => true,
            'aspect_ratio' => '16by9',
            'min_height'   => null,
            'autoplay'     => true,
            'loop'         => false,
            'muted'        => false,
            'player'       => true,
            'alt_message'  => $this->translator->trans('player.video_not_supported', [], 'EkynaMedia'),
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
        if (null !== $poster = $this->videoManager->thumb($video)) {
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
            'min_height'   => $params['min_height'],
            'alt_message'  => $params['alt_message'],
            'attr'         => $params['attr'],
        ]);
    }

    /**
     * Renders the flash swf.
     */
    public function renderFlash(MediaInterface $flash, array $params = []): string
    {
        if (!MediaTypes::isFlash($flash)) {
            throw new InvalidArgumentException('Expected media with "flash" type.');
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
     */
    public function renderAudio(MediaInterface $audio, array $params = []): string
    {
        if (!MediaTypes::isAudio($audio)) {
            throw new InvalidArgumentException('Expected media with "audio" type.');
        }

        $params = array_replace_recursive([
            'alt_message' => $this->translator->trans('player.audio_not_supported', [], 'EkynaMedia'),
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
     */
    public function renderImage(MediaInterface $image, array $params = []): string
    {
        if (!MediaTypes::isImage($image)) {
            throw new InvalidArgumentException('Expected media with "image" type.');
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
     */
    public function renderSvg(MediaInterface $svg, array $params = []): string
    {
        if (!MediaTypes::isSvg($svg)) {
            throw new InvalidArgumentException('Expected media with "svg" type.');
        }

        $path = $this->generator->generateFrontUrl($svg);

        $params = array_replace_recursive([
            'attr' => [
                'id'    => 'media-svg-' . $svg->getId(),
                'title' => $svg->getTitle() ?? '',
            ],
        ], $params);

        $mode = $params['mode'] ?? 'content';
        unset($params['mode']);

        if ($mode === 'object') {
            $params['attr']['data'] = $path;
            $params['attr']['type'] = 'image/svg+xml';

            return $this->renderBlock('object', [
                'attr'     => $params['attr'],
                'fallback' => $svg->getTitle() ?? '',
            ]);
        } elseif ($mode === 'image') {
            $params['attr']['src'] = $path;
            $params['attr']['alt'] = $svg->getTitle() ?? '';

            return $this->renderBlock('image', [
                'attr' => $params['attr'],
            ]);
        }

        // By Content
        $doc = new DOMDocument();
        $doc->loadXML($this->generator->getContent($svg));
        $nodes = $doc->getElementsByTagName('svg');
        if (1 === $nodes->length) {
            $node = $nodes->item(0);

            foreach ($params['attr'] as $key => $value) {
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                $node->setAttribute($key, $value);
            }

            return $doc->saveHTML($node);
        }

        return '';
    }

    /**
     * Renders the file (link).
     */
    public function renderFile(MediaInterface $file, array $params = []): string
    {
        if (!(MediaTypes::isFile($file) || MediaTypes::isArchive($file))) {
            throw new InvalidArgumentException('Expected media with "file" or "archive" type.');
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
     */
    public function renderVideoThumb(MediaInterface $video, array $params = []): string
    {
        if (!MediaTypes::isVideo($video)) {
            throw new InvalidArgumentException('Expected media with "video" type.');
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
            throw new RuntimeException("The 'src' attribute must be set.");
        }

        if (!(isset($params['attr']['width']) && isset($params['attr']['height']))) {
            $filter = $this->filterManager->getFilterConfiguration()->get($params['filter']);
            if (array_key_exists('filters', $filter)) {
                // TODO better size resolution
                $width = $height = 0;
                foreach ($filter['filters'] as $cfg) {
                    if (array_key_exists('size', $cfg)) {
                        $width = max($width, $cfg['size'][0]);
                        $height = max($height, $cfg['size'][1]);
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
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function renderBlock(string $blockName, array $blockVars): string
    {
        if (null === $this->template) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->template = $this->twig->load($this->templatePath);
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->template->renderBlock($blockName, $blockVars);
    }
}
