<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Controller\Admin\BrowserController;
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

        // Media (player) controller
        ->set('ekyna_media.controller.media', MediaController::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.filesystem.media'),
                service('ekyna_media.renderer'),
                service('ekyna_media.manager.video'),
                service('router'),
                service('twig'),
            ])
            ->alias(MediaController::class, 'ekyna_media.controller.media')->public()

    ;
};
