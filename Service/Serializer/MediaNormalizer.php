<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service\Serializer;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Component\Resource\Bridge\Symfony\Serializer\ResourceNormalizer;
use Exception;

/**
 * Class MediaNormalizer
 * @package Ekyna\Bundle\MediaBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaNormalizer extends ResourceNormalizer
{
    private Generator $generator;

    /**
     * Constructor.
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     *
     * @param MediaInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $folder = $object->getFolder();

        $data = [
            'id'       => $object->getId(),
            'title'    => $object->getTitle(),
            'front'    => $this->generator->generateFrontUrl($object),
            'player'   => $this->generator->generatePlayerUrl($object),
            'folderId' => $folder ? $folder->getId() : null,
        ];

        if ($this->contextHasGroup('Manager', $context)) {
            $data['path'] = $object->getPath();
            $data['size'] = $object->getSize();
            $data['type'] = $object->getType();
            $data['filename'] = $object->getFilename();
            $data['thumb'] = $this->generator->generateThumbUrl($object);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        throw new Exception('Not yet implemented');
    }
}
