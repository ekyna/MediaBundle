<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Bundle\ResourceBundle\Model\AbstractConstants;

/**
 * Class AspectRatio
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AspectRatio extends AbstractConstants
{
    public const RATIO_16_9 = '16by9';
    public const RATIO_4_3  = '4by3';
    public const RATIO_1_1  = '1by1';


    /**
     * @inheritDoc
     */
    public static function getConfig(): array
    {
        return [
            self::RATIO_16_9 => ['16:9'],
            self::RATIO_4_3  => ['4:3'],
            self::RATIO_1_1  => ['1:1'],
        ];
    }
}
