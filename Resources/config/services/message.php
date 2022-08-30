<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\MessageHandler\ConvertVideoHandler;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Convert video message handler
        ->set('ekyna_media.message_handler.convert_video', ConvertVideoHandler::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.manager.video'),
                abstract_arg('Timeout in seconds'),
            ])
            ->tag('messenger.message_handler')
    ;
};
