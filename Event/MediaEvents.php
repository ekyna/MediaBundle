<?php

namespace Ekyna\Bundle\MediaBundle\Event;

/**
 * Class MediaEvents
 * @package Ekyna\Bundle\MediaBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class MediaEvents
{
    const PRE_CREATE = 'ekyna_media.media.pre_create';
    const PRE_UPDATE = 'ekyna_media.media.pre_update';
    const PRE_DELETE = 'ekyna_media.media.pre_delete';
}
