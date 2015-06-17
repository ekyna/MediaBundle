<?php

namespace Ekyna\Bundle\MediaBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Table\Type\ResourceTableType;
use Ekyna\Component\Table\TableBuilderInterface;

/**
 * Class ImageType
 * @package Ekyna\Bundle\MediaBundle\Table\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryType extends ResourceTableType
{
    /**
     * {@inheritdoc}
     */
    public function buildTable(TableBuilderInterface $tableBuilder, array $options = array())
    {
        $tableBuilder
            ->addColumn('id', 'number', array(
                'sortable' => true,
            ))
            ->addColumn('title', 'anchor', array(
                'label' => 'ekyna_core.field.title',
                'sortable' => true,
                'route_name' => 'ekyna_media_gallery_admin_show',
                'route_parameters_map' => array(
                    'galleryId' => 'id'
                ),
            ))
            ->addColumn('updatedAt', 'datetime', array(
                'sortable' => true,
                'label' => 'ekyna_core.field.updated_at',
            ))
            ->addColumn('actions', 'admin_actions', array(
                'buttons' => array(
                    array(
                        'label' => 'ekyna_core.button.edit',
                        'class' => 'warning',
                        'route_name' => 'ekyna_media_gallery_admin_edit',
                        'route_parameters_map' => array(
                            'galleryId' => 'id'
                        ),
                        'permission' => 'edit',
                    ),
                    array(
                        'label' => 'ekyna_core.button.remove',
                        'class' => 'danger',
                        'route_name' => 'ekyna_media_gallery_admin_remove',
                        'route_parameters_map' => array(
                            'galleryId' => 'id'
                        ),
                        'permission' => 'delete',
                    ),
                ),
            ))
            /*->addFilter('id', 'number')
            ->addFilter('name', 'text', array(
                'label' => 'ekyna_core.field.name'
            ))*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_gallery';
    }
}
