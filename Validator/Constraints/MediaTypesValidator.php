<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class MediaTypesValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTypesValidator extends ConstraintValidator
{
    /**
     * @inheritdoc
     */
    public function validate($media, Constraint $constraint)
    {
        if (null === $media) {
            return;
        }

        if (!$media instanceof MediaInterface) {
            throw new UnexpectedTypeException($media, MediaInterface::class);
        }
        if (!$constraint instanceof MediaTypes) {
            throw new UnexpectedTypeException($constraint, MediaTypes::class);
        }

        if (!in_array($media->getType(), $constraint->types, true)) {
            $this->context->addViolation(
                $constraint->invalidType
            );
        }
    }
}
