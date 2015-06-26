<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class AdminMenuPass
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection\Compiler
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AdminMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_admin.menu.pool')) {
            return;
        }

        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', array(array(
            'name'     => 'content',
            'label'    => 'ekyna_core.field.content',
            'icon'     => 'file',
            'position' => 20,
        )));
        $pool->addMethodCall('createEntry', array('content', array(
            'name'     => 'medias',
            'route'    => 'ekyna_media_media_admin_home',
            'label'    => 'ekyna_media.media.label.plural',
            'resource' => 'ekyna_media_media',
            'position' => 91,
        )));
        $pool->addMethodCall('createEntry', array('content', array(
            'name'     => 'gallery',
            'route'    => 'ekyna_media_gallery_admin_home',
            'label'    => 'ekyna_media.gallery.label.plural',
            'resource' => 'ekyna_media_gallery',
            'position' => 92,
        )));
    }
}
