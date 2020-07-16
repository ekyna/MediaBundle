<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class RootFolders
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class MediaTypes extends AbstractConstants
{
    public const FILE    = 'file';
    public const IMAGE   = 'image';
    public const SVG     = 'svg';
    public const VIDEO   = 'video';
    public const FLASH   = 'flash';
    public const AUDIO   = 'audio';
    public const ARCHIVE = 'archive';


    /**
     * @inheritdoc
     */
    public static function getConfig(): array
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
     * @inheritDoc
     */
    public static function getTheme(string $constant): ?string
    {
        return null;
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
