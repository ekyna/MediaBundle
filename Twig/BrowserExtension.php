<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Controller\Admin\BrowserController;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class BrowserExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BrowserExtension extends AbstractExtension
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var SessionInterface
     */
    private $session;


    /**
     * Constructor.
     *
     * @param Generator           $generator
     * @param NormalizerInterface $normalizer
     * @param SessionInterface    $session
     */
    public function __construct(Generator $generator, NormalizerInterface $normalizer, SessionInterface $session = null)
    {
        $this->generator  = $generator;
        $this->normalizer = $normalizer;
        $this->session    = $session;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'render_media_manager',
                [$this, 'renderManager'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new TwigFilter(
                'media_thumb',
                [$this, 'renderMediaThumb'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFilter(
                'media_thumb_path',
                [$this, 'getMediaThumbPath']
            ),
        ];
    }

    /**
     * Renders the media manager.
     *
     * @param Environment $env
     * @param array       $config
     *
     * @return string
     */
    public function renderManager(Environment $env, array $config = [])
    {
        if (!isset($config['folderId']) && $this->session) {
            if (0 < $id = $this->session->get(BrowserController::SESSION_FOLDER_ID))            {
                $config['folderId'] = $id;
            }
        }

        return $env->render('@EkynaMedia/Manager/render.html.twig', [
            'config' => $config,
        ]);
    }

    /**
     * Renders the media thumb.
     *
     * @param Environment    $env
     * @param MediaInterface $media
     * @param array          $controls
     *
     * @return string
     */
    public function renderMediaThumb(Environment $env, MediaInterface $media = null, array $controls = [])
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
                throw new \InvalidArgumentException('Controls must have "role" and "icon" defined.');
            }
        }

        $data = null;
        if ($media) {
            $data = $this->normalizer->normalize($media, 'json', ['groups' => ['Manager']]);
        }

        return $env->render('@EkynaMedia/Js/thumb.html.twig', [
            'media'    => $data,
            'data'     => json_encode($data ?? [], JSON_FORCE_OBJECT),
            'controls' => $controls,
            'selector' => false,
        ]);
    }

    /**
     * Renders the media thumb.
     *
     * @param MediaInterface $media
     *
     * @return string
     */
    public function getMediaThumbPath(MediaInterface $media)
    {
        return $this->generator->generateThumbUrl($media);
    }
}
