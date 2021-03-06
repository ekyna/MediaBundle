<?php

namespace Ekyna\Bundle\MediaBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class GalleryMediaSubscriber
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryMediaSubscriber implements EventSubscriber
{
    const MEDIA_FQCN = 'Ekyna\Bundle\MediaBundle\Entity\Media';
    const GALLERY_MEDIA_INTERFACE = 'Ekyna\Bundle\MediaBundle\Model\GalleryMediaInterface';

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();

        // Prevent doctrine:generate:entities bug
        if (!class_exists($metadata->getName())) {
            return;
        }

        // Check if class implements the gallery media interface
        if (!in_array(self::GALLERY_MEDIA_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('media')) {
            return;
        }

        $metadata->mapManyToOne([
            'fieldName'     => 'media',
            'targetEntity'  => self::MEDIA_FQCN,
            'cascade'       => ['persist', 'merge', 'refresh', 'detach'],
            'joinColumns' => [
                [
                    'name'                  => 'media_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'CASCADE',
                    'nullable'              => true,
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }
}
