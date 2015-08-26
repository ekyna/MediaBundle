<?php

namespace Ekyna\Bundle\MediaBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
            $fs = $this->get('local_media_filesystem');
            if ($fs->has($key)) {
                $file = $fs->get($key);

                $lastModified = new \DateTime();
                $lastModified->setTimestamp($file->getTimestamp());

                $response = new Response();
                $response->setPublic();
                $response->setLastModified($lastModified);
                $response->setEtag(md5($file->getPath().$file->getTimestamp()));
                if ($response->isNotModified($request)) {
                    return $response;
                }

                $response = new StreamedResponse();
                $response->setPublic();
                $response->setLastModified($lastModified);
                $response->setEtag(md5($file->getPath().$file->getTimestamp()));

                // Set the headers
                $response->headers->set('Content-Type', $file->getMimetype());
                $response->headers->set('Content-Length', $file->getSize());

                $response->setCallback(function () use ($file) {
                    fpassthru($file->readStream());
                });

                return $response;
            }
        }

        throw new NotFoundHttpException('Media not found');
    }
}
