<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes as Types;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\Validator\Constraint;

use function array_key_exists;
use function is_array;
use function sprintf;

/**
 * Class MediaTypes
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTypes extends Constraint
{
    public string $invalidType = 'ekyna_media.media.invalid_type';
    public array  $types       = [];


    /**
     * Constructor.
     *
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            if (!is_array($options)) {
                $options = [$options];
            }
            if (!array_key_exists('types', $options)) {
                $options = [
                    'types' => $options,
                ];
            }
        }

        parent::__construct($options);

        $this->types = (array)$this->types;

        if (empty($this->types)) {
            throw new MissingOptionsException(
                sprintf('Option "types" must be given for constraint %s', __CLASS__),
                ['types']
            );
        }

        foreach ($this->types as $type) {
            Types::isValid($type, true);
        }
    }

    /**
     * @inheritDoc
     */
    public function getRequiredOptions(): array
    {
        return ['types'];
    }
}
