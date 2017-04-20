<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service\Serializer;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Component\Resource\Bridge\Symfony\Serializer\ResourceNormalizer;
use Exception;

/**
 * Class FolderNormalizer
 * @package Ekyna\Bundle\MediaBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class FolderNormalizer extends ResourceNormalizer
{
    /**
     * @inheritDoc
     *
     * @param FolderInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->contextHasGroup('Manager', $context)) {
            $data = [
                'level'    => $object->getLevel(),
                'key'      => $object->getId(),
                'title'    => $object->getName(),
                'folder'   => true,
                'active'   => $object->getActive(),
                'children' => [],
            ];

            foreach ($object->getChildren() as $child) {
                $data['children'][] = $this->serializer->normalize($child, $format, $context);
            }

            return $data;
        }

        return [
            'id'   => $object->getId(),
            'name' => $object->getName(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        throw new Exception('Not yet implemented.');
    }
}
