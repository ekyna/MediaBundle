<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\DependencyInjection\Compiler;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler\AdminMenuPass as CmsPass;
use Ekyna\Bundle\MediaBundle\Action\Admin\MediaListAction;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AdminMenuPass
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AdminMenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $pool = $container->getDefinition('ekyna_admin.menu.pool');

        $pool->addMethodCall('createGroup', [CmsPass::GROUP]);

        $pool->addMethodCall('createEntry', [
            'content',
            [
                'name'     => 'media',
                'resource' => 'ekyna_media.media',
                'action'   => MediaListAction::class,
                'position' => 80,
            ],
        ]);
    }
}
