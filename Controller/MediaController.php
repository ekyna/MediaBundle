<?php

namespace Ekyna\Bundle\MediaBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MediaController
 * @package Ekyna\Bundle\MediaBundle\Controller
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaController extends Controller
{
    /**
     * Download local file.
     *
     * @param Request $request
     * @return BinaryFileResponse
     * @throws NotFoundHttpException
     */
    public function downloadAction(Request $request)
    {
        $key = $request->attributes->get('key');
        if (0 < strlen($key)) {
            $gfs = $this->get('gaufrette.local_media_filesystem');
            if ($gfs->has($key)) {
                $file = 'gaufrette://local_media/'.$key;
                return new BinaryFileResponse($file);
            }
        }

        throw new NotFoundHttpException('Media not found');
    }
}
