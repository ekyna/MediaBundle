<?php

namespace Ekyna\Bundle\MediaBundle\Show\Type;

use Ekyna\Bundle\AdminBundle\Show\Exception\InvalidArgumentException;
use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Show\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function build(View $view, $value, array $options = [])
    {
        if ($value && !$value instanceof MediaInterface) {
            throw new InvalidArgumentException("Expected instance of " . MediaInterface::class);
        }

        parent::build($view, $value, $options);
    }

    /**
     * @inheritDoc
     */
    public function getWidgetPrefix()
    {
        return 'media';
    }
}