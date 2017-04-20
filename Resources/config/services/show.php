<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Show\Type\MediasType;
use Ekyna\Bundle\MediaBundle\Show\Type\MediaType;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Media show type
        ->set('ekyna_media.show_type.media', MediaType::class)
            ->args([
                service('ekyna_media.repository.media'),
            ])
            ->tag('ekyna_admin.show.type')

        // Media collection show type
        ->set('ekyna_media.show_type.medias', MediasType::class) // TODO Rename class
            ->tag('ekyna_admin.show.type')
    ;
};
