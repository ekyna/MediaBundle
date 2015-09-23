<?php

namespace Ekyna\Bundle\MediaBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends ResourceTableType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('id', 'number', [
                'sortable' => true,
            ])
            ->addColumn('title', 'anchor', [
                'label' => 'ekyna_core.field.title',
                'sortable' => true,
                'route_name' => 'ekyna_media_media_admin_show',
                'route_parameters_map' => [
                    'mediaId' => 'id'
                ],
            ])
            ->addColumn('updatedAt', 'datetime', [
                'sortable' => true,
                'label' => 'ekyna_core.field.updated_at',
            ])
            ->addColumn('actions', 'admin_actions', [
                'buttons' => [
                    [
                        'label' => 'ekyna_core.button.edit',
                        'class' => 'warning',
                        'route_name' => 'ekyna_media_media_admin_edit',
                        'route_parameters_map' => [
                            'mediaId' => 'id'
                        ],
                        'permission' => 'edit',
                    ],
                    [
                        'label' => 'ekyna_core.button.remove',
                        'class' => 'danger',
                        'route_name' => 'ekyna_media_media_admin_remove',
                        'route_parameters_map' => [
                            'mediaId' => 'id'
                        ],
                        'permission' => 'delete',
                    ],
                ],
            ])
            /*->addFilter('id', 'number')
            ->addFilter('path', 'text', array(
                'label' => 'ekyna_core.field.path'
            ))
            ->addFilter('alt', 'number', array(
                'label' => 'ekyna_core.field.alt'
            ))*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_media';
    }
}
