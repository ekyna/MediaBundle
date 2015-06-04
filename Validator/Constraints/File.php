<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class File
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class File extends Constraint
{
    public $nameIsMandatory = 'ekyna_media.uploadable.file_is_mandatory';
    public $leaveBlank      = 'ekyna_media.uploadable.leave_blank';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
