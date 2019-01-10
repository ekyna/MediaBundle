<?php

namespace Ekyna\Bundle\MediaBundle\Service\Serializer;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Component\Resource\Serializer\AbstractResourceNormalizer;

/**
 * Class MediaNormalizer
 * @package Ekyna\Bundle\MediaBundle\Service\Serializer
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaNormalizer extends AbstractResourceNormalizer
{
    /**
     * @var Generator
     */
    private $generator;


    /**
     * Sets the generator.
     *
     * @param Generator $generator
     */
    public function setGenerator(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @inheritdoc
     *
     * @param MediaInterface $media
     */
    public function normalize($media, $format = null, array $context = [])
    {
        $folder = $media->getFolder();

        $data = [
            'id'       => $media->getId(),
            'title'    => $media->getTitle(),
            'front'    => $this->generator->generateFrontUrl($media),
            'player'   => $this->generator->generatePlayerUrl($media),
            'folderId' => $folder ? $folder->getId() : null,
        ];

        if ($this->contextHasGroup('Manager', $context)) {
            $data['path'] = $media->getPath();
            $data['size'] = $media->getSize();
            $data['type'] = $media->getType();
            $data['filename'] = $media->getFilename();
            $data['thumb'] = $this->generator->generateThumbUrl($media);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        throw new \Exception('Not yet implemented');
    }

    /**
     * @inheritdoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof MediaInterface;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return false;
    }
}