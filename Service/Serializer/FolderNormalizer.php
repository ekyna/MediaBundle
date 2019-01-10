<?php

namespace Ekyna\Bundle\MediaBundle\Service\Serializer;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Class FolderNormalizer
 * @package Ekyna\Bundle\MediaBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @property \Symfony\Component\Serializer\Serializer $serializer
 */
class FolderNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @inheritdoc
     *
     * @param FolderInterface $folder
     */
    public function normalize($folder, $format = null, array $context = [])
    {
        $groups = isset($context['groups']) ? (array)$context['groups'] : [];

        if (in_array('Manager', $groups, true)) {
            $data = [
                'level'    => $folder->getLevel(),
                'key'      => $folder->getId(),
                'title'    => $folder->getName(),
                'folder'   => true,
                'active'   => $folder->getActive(),
                'children' => [],
            ];

            foreach ($folder->getChildren() as $child) {
                $data['children'][] = $this->serializer->normalize($child, $format, $context);
            }

            return $data;
        }

        return [
            'id'   => $folder->getId(),
            'name' => $folder->getName(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        throw new \Exception("Not yet implemented.");
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof FolderInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }
}
