<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Controller;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Ekyna\Bundle\ResourceBundle\Service\Filesystem\FilesystemHelper;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

use function file_exists;
use function in_array;
use function is_readable;
use function md5;

/**
 * Class MediaController
 * @package Ekyna\Bundle\MediaBundle\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaController
{
    private MediaRepositoryInterface $repository;
    private Filesystem               $filesystem;
    private Renderer                 $renderer;
    private VideoManager             $videoManager;
    private UrlGeneratorInterface    $urlGenerator;
    private Environment              $twig;

    public function __construct(
        MediaRepositoryInterface $repository,
        Filesystem $filesystem,
        Renderer $renderer,
        VideoManager $videoManager,
        UrlGeneratorInterface $urlGenerator,
        Environment $twig
    ) {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
        $this->renderer = $renderer;
        $this->videoManager = $videoManager;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    /**
     * Download local file.
     */
    public function download(Request $request): Response
    {
        $media = $this->findMedia($request->attributes->get('key'));

        $path = $media->getPath();

        $helper = new FilesystemHelper($this->filesystem);

        try {
            return $helper->createFileResponse($path);
        } catch (FilesystemException $exception) {
        }

        throw new NotFoundHttpException('File does not exist or is not available');
    }

    /**
     * Display local file.
     */
    public function player(Request $request): Response
    {
        $media = $this->findMedia($request->attributes->get('key'));

        if (in_array($media->getType(), [MediaTypes::FILE, MediaTypes::ARCHIVE], true)) {
            return new RedirectResponse(
                $this->urlGenerator->generate('ekyna_media_download', [
                    'key' => $media->getPath(),
                ])
            );
        }

        $path = $media->getPath();

        $lastModified = $media->getUpdatedAt();
        if ($request->isXmlHttpRequest()) {
            $eTag = md5('player-' . $path . '-xhr-' . $lastModified->getTimestamp());
        } else {
            $eTag = md5('player-' . $path . $lastModified->getTimestamp());
        }

        $response = new Response();
        $response->setEtag($eTag);
        $response->setLastModified($lastModified);
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        if ($request->isXmlHttpRequest()) {
            $content = $this->renderer->renderMedia($media);
            if ('true' === $request->query->get('fancybox')) {
                $content = '<div style="width:90%;max-width:1200px;">' . $content . '</div>';
            }
        } else {
            $template = "@EkynaMedia/Media/{$media->getType()}.html.twig";
            $content = $this->twig->render($template, [
                'media' => $media,
            ]);
        }

        $response->setContent($content);

        return $response;
    }

    /**
     * Video (conversion) action.
     */
    public function video(Request $request): Response
    {
        $media = $this->findMedia($request->attributes->get('key'));

        $format = $request->attributes->get('_format');

        $path = $this->videoManager->getConvertedPath($media, $format);
        $public = true;

        if (!(file_exists($path) && is_readable($path))) {
            $path = $this->videoManager->getPendingVideoPath($format);
            $public = false;

            if (!(file_exists($path) && is_readable($path))) {
                throw new NotFoundHttpException('Video not found.');
            }
        }

        BinaryFileResponse::trustXSendfileTypeHeader();

        return new BinaryFileResponse($path, Response::HTTP_OK, [], $public);
    }

    /**
     * Finds the media by its path.
     *
     * @throws NotFoundHttpException
     */
    private function findMedia(string $path): MediaInterface
    {
        $media = $this->repository->findOneByPath($path);

        if (null === $media) {
            throw new NotFoundHttpException('Media not found');
        }

        try {
            if (!$this->filesystem->fileExists($path)) {
                // Local filesystem does not throw exception
                throw new NotFoundHttpException('Media file not found.');
            }
        } catch (FilesystemException $exception) {
            throw new NotFoundHttpException('Media file not found.');
        }

        return $media;
    }
}
