<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\UploadableInterface;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class FileValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FileValidator extends ConstraintValidator
{
	/**
	 * {@inheritdoc}
	 */
    public function validate($file, Constraint $constraint)
    {
    	if (! $file instanceof UploadableInterface) {
    	    throw new UnexpectedTypeException($file, 'Ekyna\Bundle\MediaBundle\Model\UploadableInterface');
    	}
    	if (! $constraint instanceof File) {
    	    throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\File');
    	}

		/**
		 * @var File  $constraint
		 * @var UploadableInterface $file
		 */
    	if ($file->hasFile()) {
    	    if (! $file->hasRename()) {
    	        $this->context->addViolationAt(
    	            'name',
    	            $constraint->nameIsMandatory
    	        );
    	    }
    	} elseif (! $file->hasPath()) {
    	    if ($file->hasRename()) {
    	        $this->context->addViolationAt(
    	            'name',
    	            $constraint->leaveBlank
    	        );
    	    }
    	}
    }
}
