<?php

namespace Ekyna\Bundle\MediaBundle;

use Ekyna\Bundle\CoreBundle\AbstractBundle;
use Ekyna\Bundle\MediaBundle\DependencyInjection\Compiler\AdminMenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ekyna\Bundle\MediaBundle\Model;

/**
 * Class EkynaMediaBundle
 * @package Ekyna\Bundle\MediaBundle
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaMediaBundle extends AbstractBundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdminMenuPass());
    }

    /**
     * @inheritdoc
     */
    protected function getModelInterfaces()
    {
        return [
            Model\MediaInterface::class => 'ekyna_media.media.class',
        ];
    }
}
