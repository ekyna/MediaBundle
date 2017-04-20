<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\EventListener\MediaEventSubscriber;
use Ekyna\Bundle\MediaBundle\Listener\GalleryMediaSubscriber;
use Ekyna\Bundle\MediaBundle\Listener\MediaListener;
use Ekyna\Bundle\MediaBundle\Listener\MediaSubjectSubscriber;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Media subject metadata event listener
        ->set('ekyna_media.listener.media_subject_metadata', MediaSubjectSubscriber::class)
            ->tag('doctrine.event_listener', [
                'event'      => 'loadClassMetadata',
                'connection' => 'default',
            ])

        // Gallery media metadata event listener
        ->set('ekyna_media.listener.gallery_media_metadata', GalleryMediaSubscriber::class)
            ->tag('doctrine.event_listener', [
                'event'      => 'loadClassMetadata',
                'connection' => 'default',
            ])

        // Media (entity) event listener
        ->set('ekyna_media.listener.media_entity', MediaListener::class)
            ->tag('doctrine.orm.entity_listener', ['lazy' => true])

        // Media (resource) event listener
        ->set('ekyna_media.listener.media_resource', MediaEventSubscriber::class)
            ->args([
                service('ekyna_media.repository.conversion_request'),
                service('ekyna_resource.orm.persistence_helper'),
                service('ekyna_media.filesystem.video'),
            ])
            ->tag('resource.event_subscriber')
    ;
};
