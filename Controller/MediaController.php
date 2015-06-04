<?php

namespace Ekyna\Bundle\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MediaController
 * @package Ekyna\Bundle\MediaBundle\Controller
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaController
{
    /**
     * Serve local file.
     *
     * @param Request $request
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function fileAction(Request $request)
    {
        $key = $request->attributes->get('key');
        if (0 < strlen($key)) {
            $gfs = $this->get('gaufrette.local_file_filesystem');
            if ($gfs->has($key)) {
                $file = 'media://local_file/'.$key;
                return new BinaryFileResponse($file);
            }
        }

        throw new NotFoundHttpException('File not found');
    }
}
