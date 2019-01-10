<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Symfony\Component\Serializer\Serializer;

/**
 * Class BrowserExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BrowserExtension extends \Twig_Extension
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var Serializer
     */
    private $serializer;


    /**
     * Constructor.
     *
     * @param Generator  $generator
     * @param Serializer $serializer
     */
    public function __construct(Generator $generator, Serializer $serializer)
    {
        $this->generator = $generator;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
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
            new \Twig_SimpleFilter(
                'media_thumb',
                [$this, 'renderMediaThumb'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new \Twig_SimpleFilter(
                'media_thumb_path',
                [$this, 'getMediaThumbPath']
            ),
        ];
    }


    /**
     * Renders the media manager.
     *
     * @param \Twig_Environment $env
     * @param array             $config
     *
     * @return string
     */
    public function renderManager(\Twig_Environment $env, array $config = [])
    {
        return $env->render('@EkynaMedia/Manager/render.html.twig', [
            'config' => $config,
        ]);
    }

    /**
     * Renders the media thumb.
     *
     * @param \Twig_Environment $env
     * @param MediaInterface    $media
     * @param array             $controls
     *
     * @return string
     */
    public function renderMediaThumb(\Twig_Environment $env, MediaInterface $media = null, array $controls = [])
    {
        /*if (empty($controls)) {
            $controls = array(
                array('role' => 'edit',     'icon' => 'pencil'),
                //array('role' => 'delete',   'icon' => 'trash'),
                array('role' => 'download', 'icon' => 'download'),
            );
        }*/
        foreach ($controls as $control) {
            if (!(array_key_exists('role', $control) && array_key_exists('icon', $control))) {
                throw new \InvalidArgumentException('Controls must have "role" and "icon" defined.');
            }
        }

        $data = null;
        if ($media) {
            $data = $this->serializer->normalize($media, 'json', ['groups' => ['Manager']]);
        }

        return $env->render('@EkynaMedia/thumb.html.twig', [
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
