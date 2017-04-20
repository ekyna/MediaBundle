<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class Media
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Media extends Constraint
{
    public string $invalidKey    = 'ekyna_media.media.invalid_key';
    public string $invalidType   = 'ekyna_media.media.invalid_type';
    public string $typeMissMatch = 'ekyna_media.media.type_miss_match';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return MediaValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
