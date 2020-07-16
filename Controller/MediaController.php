<?php

namespace Ekyna\Bundle\MediaBundle\Controller;

use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use League\Flysystem\Adapter\Local;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MediaController
 * @package Ekyna\Bundle\MediaBundle\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaController extends Controller
{
    /**
     * Download local file.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function downloadAction(Request $request): Response
    {
        /**
         * @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media
         * @var \League\Flysystem\File                         $file
         */
        [$media, $file] = $this->findMedia($request->attributes->get('key'));

        // ---- BINARY ---- //

        /** @var \League\Flysystem\Filesystem $filesystem */
        $filesystem = $file->getFilesystem();
        $adapter = $filesystem->getAdapter();
        if ($adapter instanceof Local) {
            $path = $adapter->applyPathPrefix($file->getPath());

            BinaryFileResponse::trustXSendfileTypeHeader();

            return new BinaryFileResponse($path);
        }

        // ---- STREAMED ---- //

        if (1024 * 1024 < $size = $file->getSize()) { // Larger than 1Mo
            $response = new StreamedResponse(function () use ($file) {
                fpassthru($file->readStream());
            });

            $response->headers->set('Content-Type', $file->getMimetype());
            $response->headers->set('Content-Length', $size);
            $response->headers->set('X-Accel-Buffering', 'no');

            return $response;
        }

        // ---- CLASSIC ---- //

        $lastModified = $media->getUpdatedAt();

        $response = new Response();
        $response->setEtag(md5($file->getPath().$lastModified->getTimestamp()));
        $response->setLastModified($lastModified);
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $response->setContent($file->read());

        $response->headers->set('Content-Type', $file->getMimetype());
        $response->headers->set('Content-Length', $size);

        return $response;
    }

    /**
     * Video (conversion) action.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function videoAction(Request $request): Response
    {
        /**
         * @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media
         */
        [$media] = $this->findMedia($request->attributes->get('key'));

        $format = $request->attributes->get('_format');

        $manager = $this->get('ekyna_media.video_manager');

        $path = $manager->getConvertedPath($media, $format);
        $public = true;

        if (!(file_exists($path) && is_readable($path))) {
            $path = $manager->getPendingVideoPath($format);
            $public = false;

            if (!(file_exists($path) && is_readable($path))) {
                throw $this->createNotFoundException('Video not found.');
            }
        }

        BinaryFileResponse::trustXSendfileTypeHeader();

        return new BinaryFileResponse($path, Response::HTTP_OK, [], $public);
    }

    /**
     * Display local file.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function playerAction(Request $request): Response
    {
        /**
         * @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media
         * @var \League\Flysystem\File                         $file
         */
        [$media, $file] = $this->findMedia($request->attributes->get('key'));

        if (in_array($media->getType(), [MediaTypes::FILE, MediaTypes::ARCHIVE])) {
            return $this->redirect($this->generateUrl('ekyna_media_download', ['key' => $media->getPath()]));
        }

        $lastModified = $media->getUpdatedAt();
        if ($request->isXmlHttpRequest()) {
            $eTag = md5($file->getPath().'-xhr-'.$lastModified->getTimestamp());
        } else {
            $eTag = md5($file->getPath().$lastModified->getTimestamp());
        }

        $response = new Response();
        $response->setEtag($eTag);
        $response->setLastModified($lastModified);
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        if ($request->isXmlHttpRequest()) {
            $content = $this->get('ekyna_media.renderer')->renderMedia($media);
            if ('true' === $request->query->get('fancybox')) {
                $content = '<div style="width:90%;max-width:1200px;">'.$content.'</div>';
            }
        } else {
            $template = "EkynaMediaBundle:Media:{$media->getType()}.html.twig";
            $content = $this->renderView($template, [
                'media' => $media,
                'file'  => $file,
            ]);
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * Finds the media by his path.
     *
     * @param string $path
     *
     * @return array [\Ekyna\Bundle\MediaBundle\Model\MediaInterface, \League\Flysystem\File]
     * @throws NotFoundHttpException
     */
    private function findMedia(string $path): array
    {
        /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
        $media = $this
            ->get('ekyna_media.media.repository')
            ->findOneBy(['path' => $path]);

        if (null === $media) {
            throw new NotFoundHttpException('Media not found');
        }

        $fs = $this->get('local_media_filesystem');
        if (!$fs->has($media->getPath())) {
            throw new NotFoundHttpException('Media not found');
        }

        return [$media, $fs->get($media->getPath())];
    }
}
