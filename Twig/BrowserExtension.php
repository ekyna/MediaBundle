<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Browser\Generator;
use Ekyna\Bundle\MediaBundle\Entity\FolderRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class BrowserExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BrowserExtension extends \Twig_Extension
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
     * @var FolderRepository
     */
    private $folderRepository;

    /**
     * @var Generator
     */
    private $thumbGenerator;

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
     * @param FilesystemInterface   $filesystem
     * @param UrlGeneratorInterface $urlGenerator
     * @param FolderRepository      $folderRepository
     * @param Generator        $thumbGenerator
     */
    public function __construct(
        FilesystemInterface $filesystem,
        UrlGeneratorInterface $urlGenerator,
        FolderRepository $folderRepository,
        Generator $thumbGenerator
    ) {
        $this->filesystem       = $filesystem;
        $this->urlGenerator     = $urlGenerator;
        $this->folderRepository = $folderRepository;
        $this->thumbGenerator   = $thumbGenerator;
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
            new \Twig_SimpleFunction('render_media_thumb', [$this, 'renderMediaThumb'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_media_thumb_path', [$this, 'getMediaThumbPath']),
        ];
    }


    /**
     * Renders the media manager.
     *
     * @param array $config
     * @return string
     */
    public function renderManager(array $config = [])
    {
        return $this->managerTemplate->render(['config' => $config]);
    }

    /**
     * Renders the media thumb.
     *
     * @param MediaInterface $media
     * @param array          $controls
     * @return string
     */
    public function renderMediaThumb(MediaInterface $media = null, array $controls = [])
    {
        if (null !== $media) {
            $media->setThumb($this->thumbGenerator->generateThumbUrl($media));
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
        return $this->thumbTemplate->render([
            'media'    => $media,
            'controls' => $controls,
            'selector' => false,
        ]);
    }

    /**
     * Renders the media thumb.
     *
     * @param MediaInterface $media
     * @return string
     */
    public function getMediaThumbPath(MediaInterface $media)
    {
        return $this->thumbGenerator->generateThumbUrl($media);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_browser';
    }
}
