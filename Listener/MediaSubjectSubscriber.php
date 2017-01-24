<?php

namespace Ekyna\Bundle\MediaBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Ekyna\Bundle\MediaBundle\Model;

/**
 * Class MediaSubjectSubscriber
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaSubjectSubscriber implements EventSubscriber
{
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
        if (!in_array(Model\MediaSubjectInterface::class, class_implements($metadata->getName()))) {
            return;
        }

        // Don't add mapping twice
        if ($metadata->hasAssociation('media')) {
            return;
        }

        $metadata->mapManyToOne([
            'fieldName'    => 'media',
            'targetEntity' => Model\MediaInterface::class,
            'cascade'      => ['persist', 'detach'],
            'joinColumns'  => [
                [
                    'name'                 => 'media_id',
                    'referencedColumnName' => 'id',
                    'onDelete'             => 'SET NULL',
                    'nullable'             => true,
                ],
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }
}
