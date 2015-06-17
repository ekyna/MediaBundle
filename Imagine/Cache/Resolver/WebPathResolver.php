<?php

namespace Ekyna\Bundle\MediaBundle\Imagine\Cache\Resolver;

use Liip\ImagineBundle\Imagine\Cache\Resolver\WebPathResolver as BaseResolver;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class WebPathResolver
 * @package Ekyna\Bundle\MediaBundle\Imagine\Cache\Resolver
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class WebPathResolver extends BaseResolver
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @param Filesystem $filesystem
     * @param string $baseUrl
     * @param string $webRootDir
     * @param string $cachePrefix
     */
    public function __construct(
        Filesystem $filesystem,
        $baseUrl,
        $webRootDir,
        $cachePrefix = 'media'
    ) {
        $this->filesystem = $filesystem;

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->webRoot = rtrim(str_replace('//', '/', $webRootDir), '/');
        $this->cachePrefix = ltrim(str_replace('//', '/', $cachePrefix), '/');
        $this->cacheRoot = $this->webRoot.'/'.$this->cachePrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($path, $filter)
    {
        return sprintf('%s/%s',
            $this->baseUrl,
            $this->getFileUrl($path, $filter)
        );
    }
}
