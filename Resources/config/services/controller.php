<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Controller\Admin\BrowserController;
use Ekyna\Bundle\MediaBundle\Controller\Media\DownloadController;
use Ekyna\Bundle\MediaBundle\Controller\Media\PlayerController;
use Ekyna\Bundle\MediaBundle\Controller\Media\VideoController;
use Ekyna\Bundle\MediaBundle\Controller\MediaController;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Browser controller
        ->set('ekyna_media.controller.browser', BrowserController::class)
            ->args([
                service('ekyna_media.factory.folder'),
                service('ekyna_media.repository.folder'),
                service('ekyna_media.manager.folder'),
                service('ekyna_media.repository.media'),
                service('ekyna_media.manager.media'),
                service('ekyna_ui.modal.renderer'),
                service('twig'),
                service('validator'),
                service('serializer'),
                service('form.factory'),
                service('router'),
                service('ekyna_media.form_flow.media_import'),
            ])
            ->alias(BrowserController::class, 'ekyna_media.controller.browser')->public()

        // Media Download controller
        ->set('ekyna_media.controller.media.download', DownloadController::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.filesystem.media'),
            ])
            ->alias(DownloadController::class, 'ekyna_media.controller.media.download')->public()

        // Media Player controller
        ->set('ekyna_media.controller.media.player', PlayerController::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.filesystem.media'),
                service('router'),
                service('ekyna_media.renderer'),
                service('twig'),
            ])
            ->alias(PlayerController::class, 'ekyna_media.controller.media.player')->public()

        // Media Video controller
        ->set('ekyna_media.controller.media.video', VideoController::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.filesystem.media'),
                service('ekyna_media.manager.video'),
            ])
            ->call('setMessageQueue', [service('ekyna_resource.queue.message')])
            ->alias(VideoController::class, 'ekyna_media.controller.media.video')->public()
    ;
};
