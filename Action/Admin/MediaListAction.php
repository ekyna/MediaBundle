<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Action\Admin;

use Ekyna\Bundle\AdminBundle\Action\ListAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaListAction
 * @package Ekyna\Bundle\MediaBundle\Action\Admin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaListAction extends ListAction
{
    protected const NAME = 'admin_media_list';

    /**
     * @inheritDoc
     */
    public function __invoke(): Response
    {
        $parameters = $this->buildParameters();

        return $this
            ->render($this->options['template'], $parameters)
            ->setPrivate();
    }

    /**
     * @inheritDoc
     */
    public static function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefined('template')
            ->setAllowedTypes('template', 'string');
    }
}
