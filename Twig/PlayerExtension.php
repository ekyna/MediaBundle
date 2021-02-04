<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

/**
 * Class PlayerExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PlayerExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getTests()
    {
        return [
            new TwigTest('media_image',   [MediaTypes::class, 'isImage']),
            new twigTest('media_svg',     [MediaTypes::class, 'isSvg']),
            new twigTest('media_flash',   [MediaTypes::class, 'isFlash']),
            new twigTest('media_video',   [MediaTypes::class, 'isVideo']),
            new twigTest('media_audio',   [MediaTypes::class, 'isAudio']),
            new twigTest('media_file',    [MediaTypes::class, 'isFile']),
            new twigTest('media_archive', [MediaTypes::class, 'isArchive']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter('media_url',   [Generator::class, 'generateFrontUrl']),
            new TwigFilter('media_find',  [Renderer::class, 'findMedia']),
            new twigFilter('media',       [Renderer::class, 'renderMedia'], ['is_safe' => ['html']]),
            new twigFilter('media_video', [Renderer::class, 'renderVideo'], ['is_safe' => ['html']]),
            new twigFilter('media_flash', [Renderer::class, 'renderFlash'], ['is_safe' => ['html']]),
            new twigFilter('media_audio', [Renderer::class, 'renderAudio'], ['is_safe' => ['html']]),
            new twigFilter('media_image', [Renderer::class, 'renderImage'], ['is_safe' => ['html']]),
            new twigFilter('media_svg',   [Renderer::class, 'renderSvg'],   ['is_safe' => ['html']]),
            new twigFilter('media_file',  [Renderer::class, 'renderFile'],  ['is_safe' => ['html']]),
            new twigFilter('media_video_thumb', [Renderer::class, 'renderVideoThumb'], ['is_safe' => ['html']]),
        ];
    }
}
