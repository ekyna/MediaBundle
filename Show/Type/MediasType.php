<?php

namespace Ekyna\Bundle\MediaBundle\Show\Type;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\AdminBundle\Show\Exception\InvalidArgumentException;
use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;

/**
 * Class MediasType
 * @package Ekyna\Bundle\MediaBundle\Show\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediasType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function build(View $view, $value, array $options = [])
    {
        if (!$value instanceof Collection) {
            throw new InvalidArgumentException("Expected instance of " . Collection::class);
        }

        parent::build($view, $value, $options);

        $view->vars['value'] = array_map(function ($m) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaSubjectInterface $m */
            return $m->getMedia();
        }, $value->toArray());
    }

    /**
     * @inheritDoc
     */
    public function getWidgetPrefix()
    {
        return 'medias';
    }
}