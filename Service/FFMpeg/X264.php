<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Service\FFMpeg;

use FFMpeg\Format\Video\DefaultVideo;

/**
 * Class X264
 * @package Ekyna\Bundle\MediaBundle\Service\FFMpeg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class X264 extends DefaultVideo
{
    private bool $bFramesSupport = true;

    /**
     * Constructor.
     *
     * @param string $audioCodec
     * @param string $videoCodec
     */
    public function __construct(string $audioCodec = 'aac', string $videoCodec = 'libx264')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    /**
     * @inheritDoc
     */
    public function supportBFrames(): bool
    {
        return $this->bFramesSupport;
    }

    /**
     * @param $support
     *
     * @return X264
     */
    public function setBFramesSupport($support): X264
    {
        $this->bFramesSupport = $support;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableAudioCodecs(): array
    {
        return ['aac', 'libvo_aacenc', 'libfaac', 'libmp3lame', 'libfdk_aac'];
    }

    /**
     * @inheritDoc
     */
    public function getAvailableVideoCodecs(): array
    {
        return ['libx264'];
    }

    /**
     * @inheritDoc
     */
    public function getPasses()
    {
        return 2;
    }

    /**
     * @return int
     */
    public function getModulus(): int
    {
        return 2;
    }
}
