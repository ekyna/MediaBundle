<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function in_array;

/**
 * Class MediaTypesValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTypesValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!$value instanceof MediaInterface) {
            throw new UnexpectedTypeException($value, MediaInterface::class);
        }
        if (!$constraint instanceof MediaTypes) {
            throw new UnexpectedTypeException($constraint, MediaTypes::class);
        }

        if (!in_array($value->getType(), $constraint->types, true)) {
            $this->context->addViolation(
                $constraint->invalidType
            );
        }
    }
}
