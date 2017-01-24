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
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_admin.menu.pool')) {
            return;
        }

        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', [[
            'name'     => 'content',
            'label'    => 'ekyna_core.field.content',
            'icon'     => 'file',
            'position' => 20,
        ]]);
        $pool->addMethodCall('createEntry', ['content', [
            'name'     => 'medias',
            'route'    => 'ekyna_media_media_admin_home',
            'label'    => 'ekyna_media.media.label.plural',
            'resource' => 'ekyna_media_media',
            'position' => 91,
        ]]);
    }
}
