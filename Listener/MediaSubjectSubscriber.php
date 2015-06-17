<?php

namespace Ekyna\Bundle\MediaBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

/**
 * Class MediaSubjectSubscriber
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaSubjectSubscriber implements EventSubscriber
{
    const MEDIA_FQCN = 'Ekyna\Bundle\MediaBundle\Entity\Media';
    const SUBJECT_INTERFACE = 'Ekyna\Bundle\MediaBundle\Model\MediaSubjectInterface';

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

        // Check if class implements the subject interface
        if (!in_array(self::SUBJECT_INTERFACE, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('media')) {
            return;
        }

        $metadata->mapManyToOne(array(
            'fieldName'     => 'media',
            'targetEntity'  => self::MEDIA_FQCN,
            'cascade'       => array('persist', 'refresh', 'detach', 'merge'),
            //'orphanRemoval' => true,
            'joinColumns' => array(
                array(
                    'name'                  => 'media_id',
                    'referencedColumnName'  => 'id',
                    'onDelete'              => 'RESTRICT',
                    'nullable'              => true,
                ),
            ),
            // TODO fetch => ?
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
        );
    }
}
