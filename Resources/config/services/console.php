<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Command\ConvertVideoCommand;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Convert video command
        ->set('ekyna_media.command.convert_video', ConvertVideoCommand::class)
            ->args([
                service('ekyna_media.repository.media'),
                service('ekyna_media.manager.video'),
            ])
            ->tag('console.command')
    ;
};
