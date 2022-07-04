<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Controller\Media;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\ResourceBundle\Service\Filesystem\FilesystemHelper;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AbstractMediaController
 * @package Ekyna\Bundle\MediaBundle\Controller\Media
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AbstractMediaController
{
    public function __construct(
        protected readonly MediaRepositoryInterface $repository,
        protected readonly Filesystem               $filesystem
    ) {
    }

    /**
     * Finds the media by its path.
     *
     * @throws NotFoundHttpException
     */
    protected function findMedia(string $path): MediaInterface
    {
        $media = $this->repository->findOneByPath($path);

        if (null === $media) {
            throw new NotFoundHttpException('Media not found');
        }

        $helper = new FilesystemHelper($this->filesystem);

        if ($helper->fileExists($path, false)) {
            return $media;
        }

        throw new NotFoundHttpException('Media file not found.');
    }
}
