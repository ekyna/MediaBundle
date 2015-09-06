<?php

namespace Ekyna\Bundle\MediaBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @return Response
     * @throws NotFoundHttpException
     */
    public function downloadAction(Request $request)
    {
        /**
         * @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media
         * @var \League\Flysystem\File $file
         */
        list($media, $file) = $this->findMedia($request->attributes->get('key'));

        $lastModified = $media->getUpdatedAt();

        $response = new Response();
        $response->setPublic();
        $response->setLastModified($lastModified);
        $response->setEtag(md5($file->getPath().$lastModified->getTimestamp()));
        if ($response->isNotModified($request)) {
            return $response;
        }

        $response = new StreamedResponse();
        $response->setPublic();
        $response->setLastModified($lastModified);
        $response->setEtag(md5($file->getPath().$lastModified->getTimestamp()));

        // Set the headers
        $response->headers->set('Content-Type', $file->getMimetype());
        $response->headers->set('Content-Length', $file->getSize());

        $response->setCallback(function () use ($file) {
            fpassthru($file->readStream());
        });

        return $response;
    }

    /**
     * Download local file.
     *
     * @param Request $request
     * @return Response
     * @throws NotFoundHttpException
     */
    public function playerAction(Request $request)
    {
        /**
         * @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media
         * @var \League\Flysystem\File $file
         */
        list($media, $file) = $this->findMedia($request->attributes->get('key'));

        $lastModified = $media->getUpdatedAt();

        $response = new Response();
        $response->setPublic();
        $response->setLastModified($lastModified);
        $response->setEtag(md5($file->getPath().$file->getTimestamp()));
        if ($response->isNotModified($request)) {
            return $response;
        }

        $template = "EkynaMediaBundle:Media:{$media->getType()}.html.twig";

        $response = $this->render($template, array(
            'media' => $media,
            'file' => $file,
        ));
        $response->setPublic();
        $response->setLastModified($lastModified);
        $response->setEtag(md5($file->getPath().$lastModified->getTimestamp()));

        return $response;
    }

    /**
     * Finds the media by his path.
     *
     * @param string $path
     * @return array
     * @throws NotFoundHttpException
     */
    private function findMedia($path)
    {
        /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
        $media = $this
            ->get('ekyna_media.media.repository')
            ->findOneBy(['path' => $path])
        ;
        if (null === $media) {
            throw new NotFoundHttpException('Media not found');
        }

        $fs = $this->get('local_media_filesystem');
        if (!$fs->has($media->getPath())) {
            throw new NotFoundHttpException('Media not found');
        }
        $file = $fs->get($media->getPath());

        return array($media, $file);
    }
}
