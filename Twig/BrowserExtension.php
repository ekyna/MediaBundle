<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * Class BrowserExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BrowserExtension extends \Twig_Extension
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var \Twig_Template
     */
    private $managerTemplate;

    /**
     * @var \Twig_Template
     */
    private $thumbTemplate;


    /**
     * Constructor.
     *
     * @param Generator           $generator
     * @param SerializerInterface $serializer
     */
    public function __construct(Generator $generator, SerializerInterface $serializer)
    {
        $this->generator   = $generator;
        $this->serializer  = $serializer;
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->managerTemplate = $twig->loadTemplate('EkynaMediaBundle:Manager:render.html.twig');
        $this->thumbTemplate   = $twig->loadTemplate('EkynaMediaBundle::thumb.html.twig');
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_media_manager', [$this, 'renderManager'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('media_thumb',      [$this, 'renderMediaThumb'],  ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('media_thumb_path', [$this, 'getMediaThumbPath']),
        ];
    }


    /**
     * Renders the media manager.
     *
     * @param array $config
     * @return string
     */
    public function renderManager(array $config = array())
    {
        return $this->managerTemplate->render(array('config' => $config));
    }

    /**
     * Renders the media thumb.
     *
     * @param MediaInterface $media
     * @param array          $controls
     * @return string
     */
    public function renderMediaThumb(MediaInterface $media = null, array $controls = array())
    {
        if (null !== $media) {
            $media->setThumb($this->generator->generateThumbUrl($media));
        }
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

        $data = '{}';
        if ($media) {
            $context = SerializationContext::create()->setGroups(array('Manager'));
            $data = $this->serializer->serialize($media, 'json', $context);
        }

        return $this->thumbTemplate->render(array(
            'media'    => $media,
            'data'     => $data,
            'controls' => $controls,
            'selector' => false,
        ));
    }

    /**
     * Renders the media thumb.
     *
     * @param MediaInterface $media
     * @return string
     */
    public function getMediaThumbPath(MediaInterface $media)
    {
        return $this->generator->generateThumbUrl($media);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_browser';
    }
}

