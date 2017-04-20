<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service\SchemaOrg;

use Ekyna\Bundle\CmsBundle\Service\SchemaOrg\ProviderInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Spatie\SchemaOrg\AudioObject;
use Spatie\SchemaOrg\DataDownload;
use Spatie\SchemaOrg\ImageObject;
use Spatie\SchemaOrg\Schema;
use Spatie\SchemaOrg\VideoObject;
use Spatie\SchemaOrg\Type;

/**
 * Class MediaProvider
 * @package Ekyna\Bundle\MediaBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaProvider implements ProviderInterface
{
    protected Generator $generator;


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
    public function build(object $object): ?Type
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
     * @return ImageObject
     */
    private function buildImage(MediaInterface $media): ImageObject
    {
        $schema = Schema::imageObject();

        /*if ($media->getType() !== MediaTypes::SVG) {
            $schema->thumbnail(
                Schema::imageObject()
                    ->contentUrl($this->generator->generateThumbUrl($media))
            );
        }*/

        if (!empty($title = $media->getTitle())) {
            $schema->caption($title);
        }

        return $schema;
    }

    /**
     * @param MediaInterface $media
     *
     * @return VideoObject
     */
    private function buildVideo(MediaInterface $media): VideoObject
    {
        return Schema::videoObject();
    }

    /**
     * @param MediaInterface $media
     *
     * @return VideoObject
     */
    private function buildFlash(MediaInterface $media): VideoObject
    {
        return $this
            ->buildVideo($media)
            ->playerType('Flash');
    }

    /**
     * @param MediaInterface $media
     *
     * @return AudioObject
     */
    private function buildAudio(MediaInterface $media): AudioObject
    {
        return Schema::audioObject();
    }

    /**
     * @param MediaInterface $media
     *
     * @return DataDownload
     */
    private function buildDownload(MediaInterface $media): DataDownload
    {
        return Schema::dataDownload();
    }

    /**
     * @inheritDoc
     */
    public function supports(object $object): bool
    {
        return $object instanceof MediaInterface;
    }
}
