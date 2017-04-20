<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\DataFixtures\ORM\MediaProvider;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Fixtures provider
        ->set('ekyna_media.fixtures.media_provider', MediaProvider::class)
            ->args([
                service('ekyna_media.repository.folder'),
                service('ekyna_media.repository.media'),
            ])
            ->tag('nelmio_alice.faker.provider')
    ;
};
