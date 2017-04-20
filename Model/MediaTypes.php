<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

use function preg_match;
use function substr;

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
     * @inheritDoc
     */
    public static function getConfig(): array
    {
        $prefix = '';

        return [
            self::FILE    => [$prefix . self::FILE,    '125955'],
            self::IMAGE   => [$prefix . self::IMAGE,   'e6ab2e'],
            self::SVG     => [$prefix . self::SVG,     'e6ab2e'],
            self::VIDEO   => [$prefix . self::VIDEO,   'de4935'],
            self::FLASH   => [$prefix . self::FLASH,   'de4935'],
            self::AUDIO   => [$prefix . self::AUDIO,   'b1212a'],
            self::ARCHIVE => [$prefix . self::ARCHIVE, '63996b'],
        ];
    }

    /**
     * Guess the type by mime type.
     *
     * @param $mimeType
     *
     * @return string
     */
    public static function guessByMimeType($mimeType): string
    {
        if ($mimeType === 'image/svg+xml') {
            return self::SVG;
        }

        switch (substr($mimeType, 0, strpos($mimeType, '/'))) {
            case 'audio' :
                return self::AUDIO;
            case 'image' :
                return self::IMAGE;
            case 'video' :
                return self::VIDEO;
        }

        if (preg_match('~zip|rar|compress~', $mimeType)) {
            return self::ARCHIVE;
        }

        if ($mimeType === 'application/x-shockwave-flash') {
            return self::FLASH;
        }

        return self::FILE;
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
    public static function getColor(string $type): string
    {
        if (self::isValid($type)) {
            return self::getConfig()[$type][1];
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
    public static function isFile(MediaInterface $media): bool
    {
        return $media->getType() === self::FILE;
    }

    /**
     * Returns whether the media is of type image or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isImage(MediaInterface $media): bool
    {
        return $media->getType() === self::IMAGE;
    }

    /**
     * Returns whether the media is of type svg or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isSvg(MediaInterface $media): bool
    {
        return $media->getType() === self::SVG;
    }

    /**
     * Returns whether the media is of type video or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isVideo(MediaInterface $media): bool
    {
        return $media->getType() === self::VIDEO;
    }

    /**
     * Returns whether the media is of type flash or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isFlash(MediaInterface $media): bool
    {
        return $media->getType() === self::FLASH;
    }

    /**
     * Returns whether the media is of type audio or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isAudio(MediaInterface $media): bool
    {
        return $media->getType() === self::AUDIO;
    }

    /**
     * Returns whether the media is of type archive or not.
     *
     * @param MediaInterface $media
     *
     * @return bool
     */
    public static function isArchive(MediaInterface $media): bool
    {
        return $media->getType() === self::ARCHIVE;
    }
}
