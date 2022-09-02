<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Service\MediaRenderer;
use Ekyna\Bundle\MediaBundle\Service\TwigRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Class MediaExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaExtension extends AbstractExtension
{
    /**
     * @inheritDoc
     */
    public function getTests(): array
    {
        return [
            new TwigTest(
                'media_image',
                [MediaTypes::class, 'isImage']
            ),
            new TwigTest(
                'media_svg',
                [MediaTypes::class, 'isSvg']
            ),
            new TwigTest(
                'media_flash',
                [MediaTypes::class, 'isFlash']
            ),
            new TwigTest(
                'media_video',
                [MediaTypes::class, 'isVideo']
            ),
            new TwigTest(
                'media_audio',
                [MediaTypes::class, 'isAudio']
            ),
            new TwigTest(
                'media_file',
                [MediaTypes::class, 'isFile']
            ),
            new TwigTest(
                'media_archive',
                [MediaTypes::class, 'isArchive']
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'media_url',
                [Generator::class, 'generateFrontUrl']
            ),
            new TwigFilter(
                'media_find',
                [MediaRenderer::class, 'findMedia']
            ),
            new TwigFilter(
                'media',
                [MediaRenderer::class, 'renderMedia'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_video',
                [MediaRenderer::class, 'renderVideo'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_flash',
                [MediaRenderer::class, 'renderFlash'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_audio',
                [MediaRenderer::class, 'renderAudio'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_image',
                [MediaRenderer::class, 'renderImage'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_svg',
                [MediaRenderer::class, 'renderSvg'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_file',
                [MediaRenderer::class, 'renderFile'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_video_thumb',
                [MediaRenderer::class, 'renderVideoThumb'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_thumb',
                [TwigRenderer::class, 'renderMediaThumb'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'media_thumb_path',
                [TwigRenderer::class, 'getMediaThumbPath']
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'render_media_manager',
                [TwigRenderer::class, 'renderManager'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
