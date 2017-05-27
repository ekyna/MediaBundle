<?php

namespace Ekyna\Bundle\MediaBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type as CType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends ResourceTableType
{
    /**
     * @inheritdoc
     */
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
        $builder
            ->addColumn('title', BType\Column\AnchorType::class, [
                'label'                => 'ekyna_core.field.title',
                'sortable'             => true,
                'route_name'           => 'ekyna_media_media_admin_show',
                'route_parameters_map' => [
                    'mediaId' => 'id',
                ],
                'position'             => 10,
            ])
            ->addColumn('updatedAt', CType\Column\DateTimeType::class, [
                'sortable' => true,
                'label'    => 'ekyna_core.field.updated_at',
                'position' => 20,
            ])
            ->addColumn('actions', BType\Column\ActionsType::class, [
                'buttons' => [
                    [
                        'label'                => 'ekyna_core.button.edit',
                        'class'                => 'warning',
                        'route_name'           => 'ekyna_media_media_admin_edit',
                        'route_parameters_map' => [
                            'mediaId' => 'id',
                        ],
                        'permission'           => 'edit',
                    ],
                    [
                        'label'                => 'ekyna_core.button.remove',
                        'class'                => 'danger',
                        'route_name'           => 'ekyna_media_media_admin_remove',
                        'route_parameters_map' => [
                            'mediaId' => 'id',
                        ],
                        'permission'           => 'delete',
                    ],
                ],
            ]);
    }
}
