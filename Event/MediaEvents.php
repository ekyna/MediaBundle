<?php

namespace Ekyna\Bundle\MediaBundle\Event;

/**
 * Class MediaEvents
 * @package Ekyna\Bundle\MediaBundle\Event
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class MediaEvents
{
    const INSERT      = 'ekyna_media.media.insert';
    const UPDATE      = 'ekyna_media.media.update';
    const DELETE      = 'ekyna_media.media.delete';

    const INITIALIZE  = 'ekyna_media.media.initialize';

    const PRE_CREATE  = 'ekyna_media.media.pre_create';
    const POST_CREATE = 'ekyna_media.media.post_create';

    const PRE_UPDATE  = 'ekyna_media.media.pre_update';
    const POST_UPDATE = 'ekyna_media.media.post_update';

    const PRE_DELETE  = 'ekyna_media.media.pre_delete';
    const POST_DELETE = 'ekyna_media.media.post_delete';


    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }
}
