<?php

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Class RootFolders
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTypes
{
    const FILE  = 'file';
    const IMAGE = 'image';

    static public function isValid($const)
    {
        return in_array($const, array(self::FILE, self::IMAGE));
    }
}
