<?php

namespace Ekyna\Bundle\MediaBundle\Form\DataTransformer;

use Ekyna\Bundle\MediaBundle\Model\UploadableInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class UploadableToNullTransformer
 * @package Ekyna\Bundle\MediaBundle\Form\DataTransformer
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class UploadableToNullTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value instanceof UploadableInterface && $value->getUnlink()) {
            return null;
        }

        return $value;
    }
}
