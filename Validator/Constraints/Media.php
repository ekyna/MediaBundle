<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Media
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Media extends Constraint
{
    public $invalidKey = 'ekyna_media.media.invalid_key';
    public $invalidType = 'ekyna_media.media.invalid_type';
    public $typeMissMatch = 'ekyna_media.media.type_miss_match';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'ekyna_media_media';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
