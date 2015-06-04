<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Ekyna\Bundle\MediaBundle\Model\ImageInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ImageValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageValidator extends ConstraintValidator
{
	/**
	 * {@inheritdoc}
	 */
    public function validate($image, Constraint $constraint)
    {
    	if (! $image instanceof ImageInterface) {
    	    throw new UnexpectedTypeException($image, 'Ekyna\Bundle\MediaBundle\Model\ImageInterface');
    	}
    	if (! $constraint instanceof Image) {
    	    throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Image');
    	}

		/**
		 * @var Image $constraint
		 * @var ImageInterface $image
		 */
    	if (!($image->hasFile() || $image->hasPath()) && 0 < strlen($image->getAlt())) {
            $this->context->addViolationAt(
                'alt',
                $constraint->leaveBlank
            );
    	}
    }
}
