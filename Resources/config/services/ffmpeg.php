<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // FFMpeg
        ->set('ekyna_media.ffmpeg', FFMpeg::class)
            ->factory([FFMpeg::class, 'create'])
            ->args([
                abstract_arg('FFMpeg configuration'),
                service('logger'),
            ])

        // FFProbe
        ->set('ekyna_media.ffprobe', FFProbe::class)
            ->factory([FFProbe::class, 'create'])
            ->args([
                abstract_arg('FFProbe configuration'),
                service('logger'),
            ])
    ;
};
