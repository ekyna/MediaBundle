<?php

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class MediaFormats
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaFormats extends AbstractConstants
{
    // Video
    public const MP4  = 'mp4';
    public const WEBM = 'webm';
    public const OGG  = 'ogg';

    /**
     * @var array
     */
    private static $config;

    /**
     * @var array array
     */
    private static $typeCache = [];


    /**
     * Returns all the formats.
     *
     * @return array|string[]
     */
    public static function getFormats(): array
    {
        return [
            self::MP4,
            self::WEBM,
            self::OGG,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getConfig(): array
    {
        if (self::$config) {
            return self::$config;
        }

        return self::$config = [
            self::MP4  => [strtoupper(self::MP4), MediaTypes::VIDEO],
            self::WEBM => [strtoupper(self::WEBM), MediaTypes::VIDEO],
            self::OGG  => [strtoupper(self::OGG), MediaTypes::VIDEO],
        ];
    }

    /**
     * Returns the formats for the given media type.
     *
     * @param string $type
     *
     * @return array
     */
    public static function getFormatsByType(string $type): array
    {
        if (isset(self::$typeCache[$type])) {
            return self::$typeCache[$type];
        }

        MediaTypes::isValid($type);

        $formats = [];
        foreach (self::getConfig() as $format => $config) {
            if ($type === $config[1]) {
                $formats[] = $format;
            }
        }

        return self::$typeCache[$type] = $formats;
    }
}
