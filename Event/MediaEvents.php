<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Event;

/**
 * Class MediaEvents
 * @package Ekyna\Bundle\MediaBundle\Event
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class MediaEvents
{
    public const INSERT      = 'ekyna_media.media.insert';
    public const UPDATE      = 'ekyna_media.media.update';
    public const DELETE      = 'ekyna_media.media.delete';

    public const PRE_CREATE  = 'ekyna_media.media.pre_create';
    public const POST_CREATE = 'ekyna_media.media.post_create';

    public const PRE_UPDATE  = 'ekyna_media.media.pre_update';
    public const POST_UPDATE = 'ekyna_media.media.post_update';

    public const PRE_DELETE  = 'ekyna_media.media.pre_delete';
    public const POST_DELETE = 'ekyna_media.media.post_delete';

    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }
}
