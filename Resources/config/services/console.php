<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Command\ConversionRequestCommand;
use Ekyna\Bundle\MediaBundle\Command\ConvertVideoCommand;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Conversion request command
        ->set('ekyna_media.command.conversion_request', ConversionRequestCommand::class)
            ->args([
                service('ekyna_media.repository.conversion_request'),
                service('ekyna_media.manager.video'),
                service('doctrine.orm.entity_manager'),
                param('kernel.debug'),
            ])
            ->tag('console.command')

        // Convert video command
        ->set('ekyna_media.command.convert_video', ConvertVideoCommand::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.manager.video'),
            ])
            ->tag('console.command')
    ;
};
