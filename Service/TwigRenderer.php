<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service;

use Ekyna\Bundle\MediaBundle\Controller\Admin\BrowserController;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Environment;

use function array_key_exists;
use function json_encode;

use const JSON_FORCE_OBJECT;

/**
 * Class TwigRenderer
 * @package Ekyna\Bundle\MediaBundle\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TwigRenderer
{
    private Generator           $generator;
    private NormalizerInterface $normalizer;
    private Environment  $twig;
    private RequestStack $requestStack;


    public function __construct(
        Generator $generator,
        NormalizerInterface $normalizer,
        Environment $twig,
        RequestStack $requestStack
    ) {
        $this->generator = $generator;
        $this->normalizer = $normalizer;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    /**
     * Renders the media manager.
     */
    public function renderManager(array $config = []): string
    {
        if (!isset($config['folderId']) && $this->requestStack) {
            try {
                $id = $this
                    ->requestStack
                    ->getSession()
                    ->get(BrowserController::SESSION_FOLDER_ID);

                if (0 < $id) {
                    $config['folderId'] = $id;
                }
            } catch (SessionNotFoundException $exception) {
            }
        }

        return $this->twig->render('@EkynaMedia/Manager/render.html.twig', [
            'config' => $config,
        ]);
    }

    /**
     * Renders the media thumb.
     */
    public function renderMediaThumb(MediaInterface $media = null, array $controls = []): string
    {
        if ($media && empty($controls)) {
            $controls = [
                ['role' => 'show', 'icon' => 'play', 'title' => 'Preview'],
                ['role' => 'download', 'icon' => 'download', 'title' => 'Download'],
                ['role' => 'browse', 'icon' => 'folder-open', 'title' => 'Browse'],
            ];
        }

        foreach ($controls as $control) {
            if (!(array_key_exists('role', $control) && array_key_exists('icon', $control))) {
                throw new InvalidArgumentException('Controls must have "role" and "icon" defined.');
            }
        }

        $data = null;
        if ($media) {
            $data = $this->normalizer->normalize($media, 'json', ['groups' => ['Manager']]);
        }

        return $this->twig->render('@EkynaMedia/Js/thumb.html.twig', [
            'media'    => $data,
            'data'     => json_encode($data ?? [], JSON_FORCE_OBJECT),
            'controls' => $controls,
            'selector' => false,
        ]);
    }

    /**
     * Renders the media thumb.
     */
    public function getMediaThumbPath(MediaInterface $media): string
    {
        return $this->generator->generateThumbUrl($media);
    }
}
