<?php

namespace Ekyna\Bundle\MediaBundle\Service\FFMpeg;

use FFMpeg\Format\Video\DefaultVideo;

/**
 * Class X264
 * @package Ekyna\Bundle\MediaBundle\Service\FFMpeg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class X264 extends DefaultVideo
{
    /** @var boolean */
    private $bframesSupport = true;

    public function __construct($audioCodec = 'aac', $videoCodec = 'libx264')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    /**
     * {@inheritDoc}
     */
    public function supportBFrames()
    {
        return $this->bframesSupport;
    }

    /**
     * @param $support
     *
     * @return \FFMpeg\Format\Video\X264
     */
    public function setBFramesSupport($support)
    {
        $this->bframesSupport = $support;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return array('aac', 'libvo_aacenc', 'libfaac', 'libmp3lame', 'libfdk_aac');
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableVideoCodecs()
    {
        return array('libx264');
    }

    /**
     * {@inheritDoc}
     */
    public function getPasses()
    {
        return 2;
    }

    /**
     * @return int
     */
    public function getModulus()
    {
        return 2;
    }
}
