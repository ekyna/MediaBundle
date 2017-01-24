<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes as Types;
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
     * @inheritdoc
     */
    public function validate($media, Constraint $constraint)
    {
        if (! $media instanceof MediaInterface) {
            throw new UnexpectedTypeException($media, MediaInterface::class);
        }
        if (! $constraint instanceof Media) {
            throw new UnexpectedTypeException($constraint, Media::class);
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
                    $this->context
                        ->buildViolation($constraint->invalidKey)
                        ->atPath('key')
                        ->addViolation();
                }
            }

            $propertyPath = $media->hasFile() ? 'file' : 'key';
            $type = Types::guessByMimeType($mimeType);
            if (null !== $media->getType() && $media->getType() != $type) {
                $this->context
                    ->buildViolation($constraint->typeMissMatch)
                    ->atPath($propertyPath)
                    ->addViolation();

            } elseif (null === $media->getType()) {
                $media->setType($type);
            }

            if (!Types::isValid($media->getType())) {
                $this->context->addViolation($constraint->invalidType);
            }
        }
    }
}
