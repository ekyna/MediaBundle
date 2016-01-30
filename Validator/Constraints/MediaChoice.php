<?php

namespace Ekyna\Bundle\MediaBundle\Validator\Constraints;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Validator\Constraint;

/**
 * Class MediaChoice
 * @package Ekyna\Bundle\MediaBundle\Validator\Constraints
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaChoice extends Constraint
{
    public $invalidType = 'ekyna_media.media.invalid_type';
    public $types;


    /**
     * Constructor.
     *
     * @param mixed $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->types = (array) $this->types;
        if (count($this->types)) {
            foreach ($this->types as $type) {
                MediaTypes::isValid($type, true);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'types';
    }
}
