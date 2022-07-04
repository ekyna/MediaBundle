<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Controller\Media;

use Ekyna\Bundle\MediaBundle\Message\ConvertVideo;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Ekyna\Component\Resource\Message\MessageQueueAwareTrait;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function file_exists;
use function is_readable;

/**
 * Class VideoController
 * @package Ekyna\Bundle\MediaBundle\Controller\Media
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class VideoController extends AbstractMediaController
{
    use MessageQueueAwareTrait;

    public function __construct(
                           MediaRepositoryInterface $repository,
                           Filesystem               $filesystem,
        protected readonly VideoManager             $videoManager,
    ) {
        parent::__construct($repository, $filesystem);
    }

    public function __invoke(Request $request): Response
    {
        $media = $this->findMedia($request->attributes->get('key'));

        $format = $request->attributes->get('_format');

        $path = $this->videoManager->getConvertedPath($media, $format);
        $public = true;

        if (!(file_exists($path) && is_readable($path))) {
            $this->messageQueue->addMessage(new ConvertVideo(
                $media->getId(),
                $media->getPath(),
                $format,
                false
            ));

            $path = $this->videoManager->getPendingVideoPath($format);
            $public = false;

            if (!(file_exists($path) && is_readable($path))) {
                throw new NotFoundHttpException('Video not found.');
            }
        }

        BinaryFileResponse::trustXSendfileTypeHeader();

        return new BinaryFileResponse($path, Response::HTTP_OK, [], $public);
    }
}
