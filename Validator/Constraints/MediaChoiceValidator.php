<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class MediaChoiceValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaChoiceValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($media, Constraint $constraint)
    {
        if (null === $media) {
            return;
        }

        if (! $media instanceof MediaInterface) {
            throw new UnexpectedTypeException($media, 'Ekyna\Bundle\MediaBundle\Model\MediaInterface');
        }
        if (! $constraint instanceof MediaChoice) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\MediaChoice');
        }

        if (!count($constraint->types)) {
            return;
        }

        if (!in_array($media->getType(), $constraint->types, true)) {
            $this->context->addViolation(
                $constraint->invalidType
            );
        }
    }
}
