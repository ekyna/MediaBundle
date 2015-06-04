<?php

namespace Ekyna\Bundle\MediaBundle\Twig;

use Ekyna\Bundle\MediaBundle\Entity\FolderRepository;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Gaufrette\Filesystem;
use Ekyna\Bundle\MediaBundle\Model\FileInterface;
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
     * @var \Twig_Template
     */
    private $managerTemplate;


    /**
     * Constructor.
     *
     * @param Filesystem            $filesystem
     * @param UrlGeneratorInterface $urlGenerator
     * @param FolderRepository      $folderRepository
     */
    public function __construct(
        Filesystem $filesystem,
        UrlGeneratorInterface $urlGenerator,
        FolderRepository $folderRepository
    ) {
        $this->filesystem       = $filesystem;
        $this->urlGenerator     = $urlGenerator;
        $this->folderRepository = $folderRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->managerTemplate = $twig->loadTemplate('EkynaMediaBundle:Manager:render.html.twig');
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
        );
    }

    /**
     * Renders the video.
     *
     * @param FileInterface $video
     * @return string
     */
    public function renderVideo(FileInterface $video)
    {
        return strtr(self::VIDEO_HTML5, array(
            '%aspect_ratio%' => '16by9',
            '%width%' => '720',
            '%height%' => '480',
            '%src%' => $this->urlGenerator->generate('ekyna_media_file', array('key' => $video->getPath())),
            '%mime_type%' => $this->filesystem->mimeType($video->getPath()),
        ));
    }

    /**
     * Renders the media manager.
     *
     * @param string $root
     * @return string
     * @throws \Exception
     */
    public function renderManager($root = MediaTypes::IMAGE)
    {
        if (!MediaTypes::isValid($root)) {
            throw new \InvalidArgumentException(sprintf('Undefined media manager "%s" root.', $root));
        }

        if (null === $rootFolder = $this->folderRepository->findRootByName($root)) {
            throw new \RuntimeException('Root folder not found.');
        }

        return $this->managerTemplate->render(array(
            'root' => $root,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media';
    }
}
