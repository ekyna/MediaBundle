<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Show\Type;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\AdminBundle\Show\Exception\UnexpectedTypeException;
use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\MediaBundle\Model\MediaSubjectInterface;

use function array_map;

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
    public function build(View $view, $value, array $options = []): void
    {
        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($value, Collection::class);
        }

        parent::build($view, $value, $options);

        $view->vars['value'] = array_map(function ($m) {
            /** @var MediaSubjectInterface $m */
            return $m->getMedia();
        }, $value->toArray());
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'media_medias';
    }
}
