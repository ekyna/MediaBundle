<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Browser\ThumbGenerator;
use Ekyna\Bundle\MediaBundle\Entity\FolderRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MediaExtension
 * @package Ekyna\Bundle\MediaBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaExtension extends \Twig_Extension
{
    const VIDEO_HTML5 = <<<HTML
<div class="embed-responsive embed-responsive-%aspect_ratio%">
    <video class="video embed-responsive-item" controls width="%width%" height="%height%">
        <source src="%src%" type="%mime_type%" />
        Your browser does not support the video tag.
    </video>
</div>
HTML;

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
     * @var ThumbGenerator
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
     * @param ThumbGenerator        $thumbGenerator
     */
    public function __construct(
        FilesystemInterface $filesystem,
        UrlGeneratorInterface $urlGenerator,
        FolderRepository $folderRepository,
        ThumbGenerator $thumbGenerator
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
        $this->thumbTemplate = $twig->loadTemplate('EkynaMediaBundle::thumb.html.twig');
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
            new \Twig_SimpleFilter('video', array($this, 'renderVideo'), array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_media_manager', array($this, 'renderManager'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('render_media_thumb', array($this, 'renderMediaThumb'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('get_media_thumb_path', array($this, 'getMediaThumbPath')),
        );
    }

    /**
     * Renders the video.
     *
     * @param MediaInterface $video
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderVideo(MediaInterface $video)
    {
        if ($video->getType() !== MediaTypes::VIDEO) {
            throw new \InvalidArgumentException('Expected media with "video" type.');
        }
        return strtr(self::VIDEO_HTML5, array(
            '%aspect_ratio%' => '16by9',
            '%width%' => '720',
            '%height%' => '480',
            '%src%' => $this->urlGenerator->generate('ekyna_media_download', array('key' => $video->getPath())),
            '%mime_type%' => $this->filesystem->getMimetype($video->getPath()),
        ));
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
            $media->setThumb($this->thumbGenerator->generate($media));
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
        return $this->thumbTemplate->render(array(
            'media'    => $media,
            'controls' => $controls
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
        return $this->thumbGenerator->generate($media);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media';
    }
}
