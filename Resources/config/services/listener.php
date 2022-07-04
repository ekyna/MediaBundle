<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Doctrine\ORM\Events;
use Ekyna\Bundle\MediaBundle\Event\MediaEvents;
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
                'event'      => Events::loadClassMetadata,
                'connection' => 'default',
            ])

        // Gallery media metadata event listener
        ->set('ekyna_media.listener.gallery_media_metadata', GalleryMediaSubscriber::class)
            ->tag('doctrine.event_listener', [
                'event'      => Events::loadClassMetadata,
                'connection' => 'default',
            ])

        // Media (entity) event listener
        ->set('ekyna_media.listener.media_entity', MediaListener::class)
            ->tag('doctrine.orm.entity_listener', ['lazy' => true])

        // Media (resource) event listener
        ->set('ekyna_media.listener.media_resource', MediaEventSubscriber::class)
            ->args([
                service('ekyna_resource.orm.persistence_helper'),
                service('ekyna_media.filesystem.video'),
            ])
            ->call('setMessageQueue', [service('ekyna_resource.queue.message')])
            ->tag('resource.event_listener', [
                'event'  => MediaEvents::INSERT,
                'method' => 'onInsert',
            ])
            ->tag('resource.event_listener', [
                'event'  => MediaEvents::UPDATE,
                'method' => 'onUpdate',
            ])
            ->tag('resource.event_listener', [
                'event'  => MediaEvents::DELETE,
                'method' => 'onDelete',
            ])
    ;
};
