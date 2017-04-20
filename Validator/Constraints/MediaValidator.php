<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes as Types;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class MediaValidator
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaValidator extends ConstraintValidator
{
    private FilesystemOperator $tmpFilesystem;


    /**
     * Constructor.
     *
     * @param FilesystemOperator $tmpFilesystem
     */
    public function __construct(FilesystemOperator $tmpFilesystem)
    {
        $this->tmpFilesystem = $tmpFilesystem;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof MediaInterface) {
            throw new UnexpectedTypeException($value, MediaInterface::class);
        }
        if (!$constraint instanceof Media) {
            throw new UnexpectedTypeException($constraint, Media::class);
        }

        /**
         * @var Media          $constraint
         * @var MediaInterface $value
         */
        if ($value->hasFile() || $value->hasKey()) {
            $mimeType = null;
            if ($value->hasFile()) {
                $mimeType = $value->getFile()->getMimeType();
            } elseif ($value->hasKey()) {
                try {
                    $this->tmpFilesystem->fileExists($value->getKey());
                    $mimeType = $this->tmpFilesystem->mimeType($value->getKey());
                } catch (FilesystemException $exception) {
                    $this->context
                        ->buildViolation($constraint->invalidKey)
                        ->addViolation();

                    return;
                }
            }

            $propertyPath = $value->hasFile() ? 'file' : 'key';
            $type = Types::guessByMimeType($mimeType);
            if (null !== $value->getType() && $type !== $value->getType()) {
                $this->context
                    ->buildViolation($constraint->typeMissMatch)
                    ->atPath($propertyPath)
                    ->addViolation();
            } elseif (null === $value->getType()) {
                $value->setType($type);
            }

            if (!Types::isValid($value->getType())) {
                $this->context->addViolation($constraint->invalidType);
            }
        }
    }
}
