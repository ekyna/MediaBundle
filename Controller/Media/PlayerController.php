<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Controller\Media;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Service\MediaRenderer;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

use function in_array;
use function md5;

/**
 * Class PlayerController
 * @package Ekyna\Bundle\MediaBundle\Controller\Media
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PlayerController extends AbstractMediaController
{
    public function __construct(
                           MediaRepositoryInterface $repository,
                           Filesystem               $filesystem,
        protected readonly UrlGeneratorInterface    $urlGenerator,
        protected readonly MediaRenderer            $renderer,
        protected readonly Environment              $twig,
    ) {
        parent::__construct($repository, $filesystem);
    }

    public function __invoke(Request $request): Response
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
}
