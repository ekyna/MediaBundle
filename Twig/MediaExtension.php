<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Browser\ThumbGenerator;
use Ekyna\Bundle\MediaBundle\Entity\FolderRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Gaufrette\Filesystem;
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
     * @var Filesystem
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
    private $uiTemplate;


    /**
     * Constructor.
     *
     * @param Filesystem            $filesystem
     * @param UrlGeneratorInterface $urlGenerator
     * @param FolderRepository      $folderRepository
     * @param ThumbGenerator        $thumbGenerator
     */
    public function __construct(
        Filesystem $filesystem,
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
        $this->uiTemplate = $twig->loadTemplate('EkynaMediaBundle::ui.html.twig');
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
     */
    public function renderVideo(MediaInterface $video)
    {
        // TODO check type
        return strtr(self::VIDEO_HTML5, array(
            '%aspect_ratio%' => '16by9',
            '%width%' => '720',
            '%height%' => '480',
            '%src%' => $this->urlGenerator->generate('ekyna_media_download', array('key' => $video->getPath())),
            '%mime_type%' => $this->filesystem->mimeType($video->getPath()),
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
     * @return string
     */
    public function renderMediaThumb(MediaInterface $media = null)
    {
        if (null !== $media) {
            $media->setThumb($this->thumbGenerator->generate($media));
        }
        return $this->uiTemplate->renderBlock('thumb', array('media' => $media));
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
