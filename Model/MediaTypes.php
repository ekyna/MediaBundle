<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class RootFolders
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTypes extends AbstractConstants
{
    const FILE    = 'file';
    const IMAGE   = 'image';
    const SVG     = 'svg';
    const VIDEO   = 'video';
    const FLASH   = 'flash';
    const AUDIO   = 'audio';
    const ARCHIVE = 'archive';


    /**
     * @inheritdoc
     */
    public static function getConfig()
    {
        $prefix = '';

        return [
            static::FILE    => [$prefix . static::FILE,    '125955'],
            static::IMAGE   => [$prefix . static::IMAGE,   'e6ab2e'],
            static::SVG     => [$prefix . static::SVG,     'e6ab2e'],
            static::VIDEO   => [$prefix . static::VIDEO,   'de4935'],
            static::FLASH   => [$prefix . static::FLASH,   'de4935'],
            static::AUDIO   => [$prefix . static::AUDIO,   'b1212a'],
            static::ARCHIVE => [$prefix . static::ARCHIVE, '63996b'],
        ];
    }

    /**
     * Guess the type by mime type.
     *
     * @param $mimeType
     *
     * @return string
     */
    public static function guessByMimeType($mimeType)
    {
        if ($mimeType === 'image/svg+xml') {
            return static::SVG;
        }

        switch (substr($mimeType, 0, strpos($mimeType, '/'))) {
            case 'audio' :
                return static::AUDIO;
                break;
            case 'image' :
                return static::IMAGE;
                break;
            case 'video' :
                return static::VIDEO;
                break;
        }

        if (preg_match('~zip|rar|compress~', $mimeType)) {
            return static::ARCHIVE;
        }

        if ($mimeType === 'application/x-shockwave-flash') {
            return static::FLASH;
        }

        return static::FILE;
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

    /**
     * Returns whether the media is of type file or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isFile(MediaInterface $media)
    {
        return $media->getType() === static::FILE;
    }

    /**
     * Returns whether the media is of type image or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isImage(MediaInterface $media)
    {
        return $media->getType() === static::IMAGE;
    }

    /**
     * Returns whether the media is of type svg or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isSvg(MediaInterface $media)
    {
        return $media->getType() === static::SVG;
    }

    /**
     * Returns whether the media is of type video or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isVideo(MediaInterface $media)
    {
        return $media->getType() === static::VIDEO;
    }

    /**
     * Returns whether the media is of type flash or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isFlash(MediaInterface $media)
    {
        return $media->getType() === static::FLASH;
    }

    /**
     * Returns whether the media is of type audio or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isAudio(MediaInterface $media)
    {
        return $media->getType() === static::AUDIO;
    }

    /**
     * Returns whether the media is of type archive or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isArchive(MediaInterface $media)
    {
        return $media->getType() === static::ARCHIVE;
    }
}
