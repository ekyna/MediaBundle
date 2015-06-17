<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\CoreBundle\Model\AbstractConstants;

/**
 * Class RootFolders
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTypes extends AbstractConstants
{
    const FILE    = 'file';
    const IMAGE   = 'image';
    const VIDEO   = 'video';
    const AUDIO   = 'audio';
    const ARCHIVE = 'archive';

    /**
     * {@inheritdoc}
     */
    static public function getConfig()
    {
        $prefix = '';
        return array(
            self::FILE    => array($prefix.self::FILE,    '125955'),
            self::IMAGE   => array($prefix.self::IMAGE,   'e6ab2e'),
            self::VIDEO   => array($prefix.self::VIDEO,   'de4935'),
            self::AUDIO   => array($prefix.self::AUDIO,   'b1212a'),
            self::ARCHIVE => array($prefix.self::ARCHIVE, '63996b'),
        );
    }

    /**
     * Guess the type by mime type.
     *
     * @param $mimeType
     * @return string
     */
    static public function guessByMimeType($mimeType)
    {
        switch (substr($mimeType, 0, strpos($mimeType, '/'))) {
            case 'audio' : return self::AUDIO; break;
            case 'image' : return self::IMAGE; break;
            case 'video' : return self::VIDEO; break;
        }

        if (preg_match('~zip|rar|compress~', $mimeType)) {
            return self::ARCHIVE;
        }

        return self::FILE;
    }

    /**
     * Returns the background color for the given type.
     *
     * @param string $type
     *
     * @return string
     */
    public static function getColor($type)
    {
        if (static::isValid($type)) {
            return static::getConfig()[$type][1];
        }
        return '595959';
    }
}
