<?php

namespace Ekyna\Bundle\MediaBundle\Browser;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\RGB;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Imagine\Exception\RuntimeException as ImagineException;
use Imagine\Image\Point;

/**
 * Class ThumbGenerator
 * @package Ekyna\Bundle\MediaBundle\Browser
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ThumbGenerator
{
    const DEFAULT_THUMB = '/bundles/ekynamedia/img/file.jpg';
    const NONE_THUMB = '/bundles/ekynamedia/img/media-none.jpg';

    /**
     * @var \Imagine\Image\ImagineInterface
     */
    private $imagine;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var string
     */
    private $iconsSourcePath;

    /**
     * @var string
     */
    private $webRootDirectory;

    /**
     * @var string
     */
    private $thumbsDirectory;

    /**
     * Constructor.
     *
     * @param ImagineInterface $imagine
     * @param CacheManager $cacheManager
     * @param string $webRootDirectory
     * @param string $thumbsDirectory
     */
    public function __construct(
        ImagineInterface $imagine,
        CacheManager $cacheManager,
        $webRootDirectory,
        $thumbsDirectory
    ) {
        $this->imagine = $imagine;
        $this->cacheManager = $cacheManager;

        $this->webRootDirectory = realpath($webRootDirectory);
        $this->thumbsDirectory = $thumbsDirectory;
        $this->iconsSourcePath = realpath(__DIR__.'/../Resources/extensions');

        $this->fs = new Filesystem();
    }

    /**
     * Generates a thumb for the given media.
     *
     * @param MediaInterface $media
     * @return string
     */
    public function generate(MediaInterface $media)
    {
        $thumbPath = null;

        if ($media->getType() === MediaTypes::IMAGE) {
            $thumbPath = $this->cacheManager->getBrowserPath($media->getPath(), 'media_thumb');
        } else {
            $thumbPath = $this->generateFileThumb($media);
        }

        if (null === $thumbPath) {
            $thumbPath = '/bundles/ekynamedia/img/file.jpg';
        }

        return $thumbPath;
    }

    /**
     * Generates thumb for non-image elements.
     *
     * @param MediaInterface $media
     * @return null|string
     */
    private function generateFileThumb(MediaInterface $media)
    {
        $extension   = $media->guessExtension();
        $thumbPath   = sprintf('/%s/%s.jpg', $this->thumbsDirectory, $extension);
        $destination = $this->webRootDirectory . $thumbPath;

        if (file_exists($destination)) {
            return $thumbPath;
        }

        $backgroundColor = MediaTypes::getColor($media->getType());

        $iconPath = sprintf('%s/%s.png', $this->iconsSourcePath, $extension);
        if (! file_exists($iconPath)) {
            $iconPath = $this->iconsSourcePath.'/default.png';
        }

        $this->checkDir(dirname($destination));
        try {
            $palette = new RGB();
            $thumb = $this->imagine->create(new Box(120, 90), $palette->color($backgroundColor));

            $icon = $this->imagine->open($iconPath);
            $iconSize = $icon->getSize();
            $start = new Point(120/2 - $iconSize->getWidth()/2, 90/2 - $iconSize->getHeight()/2);

            $thumb->paste($icon, $start);
            $thumb->save($destination);

            return $thumbPath;
        } catch (ImagineException $e) {
            // Image thumb generation failed
        }

        return null;
    }

    /**
     * Returns the default thumb path.
     *
     * @return string
     */
    public function getDefaultThumb()
    {
        return self::DEFAULT_THUMB;
    }

    /**
     * Returns the none thumb path.
     *
     * @return string
     */
    public function getNoneThumb()
    {
        return self::NONE_THUMB;
    }

    /**
     * Creates the directory if it does not exists.
     *
     * @param $dir
     */
    private function checkDir($dir)
    {
        if (! $this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }
}
