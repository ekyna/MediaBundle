<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Controller\Media;

use Ekyna\Bundle\ResourceBundle\Service\Filesystem\FilesystemHelper;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DownloadController
 * @package Ekyna\Bundle\MediaBundle\Controller\Media
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class DownloadController extends AbstractMediaController
{
    public function __invoke(Request $request): Response
    {
        $media = $this->findMedia($request->attributes->get('key'));

        $path = $media->getPath();

        $helper = new FilesystemHelper($this->filesystem);

        try {
            return $helper->createFileResponse($path);
        } catch (FilesystemException) {
        }

        throw new NotFoundHttpException('File does not exist or is not available');
    }
}
