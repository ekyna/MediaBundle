<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use League\Flysystem\MountManager;
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
	 * @var MountManager
	 */
	private $mountManager;

	/**
	 * Constructor.
	 *
	 * @param MountManager $mountManager
	 */
	public function __construct(MountManager $mountManager)
	{
		$this->mountManager = $mountManager;
	}

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
		if ($media->hasFile() || $media->hasKey()) {
			$mimeType = null;
			if ($media->hasFile()) {
				$mimeType = $media->getFile()->getMimeType();
			} elseif ($media->hasKey()) {
                try {
                    if (!$this->mountManager->has($media->getKey())) {
                        throw new \InvalidArgumentException();
                    }
                    $mimeType = $this->mountManager->getMimetype($media->getKey());
                } catch(\InvalidArgumentException $e) {
                    $this->context->addViolationAt('key', $constraint->invalidKey);
                }
			}

            $propertyPath = $media->hasFile() ? 'file' : 'key';
			$type = MediaTypes::guessByMimeType($mimeType);
			if (null !== $media->getType() && $media->getType() != $type) {
				$this->context->addViolationAt($propertyPath, $constraint->typeMissMatch);
			} elseif (null === $media->getType()) {
				$media->setType($type);
			}

            if (!MediaTypes::isValid($media->getType())) {
                $this->context->addViolation($constraint->invalidType);
            }
		}
    }
}
