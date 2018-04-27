<?php

namespace Ekyna\Bundle\MediaBundle\Service\SchemaOrg;

use Ekyna\Bundle\CmsBundle\Service\SchemaOrg\ProviderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Spatie\SchemaOrg\Schema;

/**
 * Class MediaProvider
 * @package Ekyna\Bundle\MediaBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaProvider implements ProviderInterface
{
    /**
     * @var Generator
     */
    protected $generator;


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
    public function build($object)
    {
        switch ($object->getType()) {
            case MediaTypes::IMAGE:
            case MediaTypes::SVG:
                $schema = $this->buildImage($object);
                break;

            case MediaTypes::VIDEO:
            case MediaTypes::FLASH:
                $schema = $this->buildVideo($object);
                break;

            case MediaTypes::AUDIO:
                $schema = $this->buildAudio($object);
                break;

            default:
                $schema = $this->buildDownload($object);
                break;
        }

        $schema
            ->contentUrl($this->generator->generateFrontUrl($object))
            ->contentSize($object->getSize())
            ->uploadDate($object->getUpdatedAt());

        return $schema;
    }

    /**
     * @param MediaInterface $media
     *
     * @return \Spatie\SchemaOrg\ImageObject
     */
    private function buildImage(MediaInterface $media)
    {
        $schema = Schema::imageObject()
            /*->thumbnail(
                Schema::imageObject()
                    ->contentUrl($this->generator->generateThumbUrl($media))
            )*/;

        if (!empty($title = $media->getTitle())) {
            $schema->caption($title);
        }

        return $schema;
    }

    /**
     * @param MediaInterface $media
     *
     * @return \Spatie\SchemaOrg\VideoObject
     */
    private function buildVideo(MediaInterface $media)
    {
        return Schema::videoObject();
    }

    /**
     * @param MediaInterface $media
     *
     * @return \Spatie\SchemaOrg\VideoObject
     */
    private function buildFlash(MediaInterface $media)
    {
        return $this
            ->buildVideo($media)
            ->playerType('Flash');
    }

    /**
     * @param MediaInterface $media
     *
     * @return \Spatie\SchemaOrg\AudioObject
     */
    private function buildAudio(MediaInterface $media)
    {
        return Schema::audioObject();
    }

    /**
     * @param MediaInterface $media
     *
     * @return \Spatie\SchemaOrg\DataDownload
     */
    private function buildDownload(MediaInterface $media)
    {
        return Schema::dataDownload();
    }

    /**
     * @inheritDoc
     */
    public function supports($object)
    {
        return $object instanceof MediaInterface;
    }
}