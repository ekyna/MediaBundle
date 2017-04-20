<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Ekyna\Bundle\MediaBundle\Model;

use function class_exists;
use function is_subclass_of;

/**
 * Class MediaSubjectSubscriber
 * @package Ekyna\Bundle\MediaBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaSubjectSubscriber
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $metadata = $eventArgs->getClassMetadata();

        // Prevent doctrine:generate:entities bug
        if (!class_exists($metadata->getName())) {
            return;
        }

        // Check if class implements the subject interface
        if (!is_subclass_of($metadata->getName(), Model\MediaSubjectInterface::class)) {
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
}
