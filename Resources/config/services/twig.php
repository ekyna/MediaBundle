<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Service\TwigRenderer;
use Ekyna\Bundle\MediaBundle\Twig\MediaExtension;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Media twig renderer
        ->set('ekyna_media.twig.renderer', TwigRenderer::class)
            ->args([
                service('ekyna_media.generator'),
                service('serializer'),
                service('twig'),
                service('request_stack'),
            ])
            ->tag('twig.runtime')

        // Media twig extension
        ->set('ekyna_media.twig.media_extension', MediaExtension::class)
            ->tag('twig.extension')
    ;
};
