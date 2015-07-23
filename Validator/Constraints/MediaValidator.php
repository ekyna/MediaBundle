<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class MediaValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaValidator extends ConstraintValidator
{
	/**
	 * {@inheritdoc}
	 */
    public function validate($media, Constraint $constraint)
    {
    	if (! $media instanceof MediaInterface) {
    	    throw new UnexpectedTypeException($media, 'Ekyna\Bundle\MediaBundle\Model\MediaInterface');
    	}
    	if (! $constraint instanceof Media) {
    	    throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Media');
    	}

		/**
		 * @var Media          $constraint
		 * @var MediaInterface $media
		 */
    	if ($media->hasFile()) {
			if (!MediaTypes::isValid($media->getType())) {
				$this->context->addViolationAt(
					'file',
					$constraint->invalidType
				);
			}
			$type = MediaTypes::guessByMimeType($media->getFile()->getMimeType());
			if ($type !== $media->getType()) {
				$this->context->addViolationAt(
					'file',
					$constraint->typeMissMatch
				);
			}
    	}
    }
}
