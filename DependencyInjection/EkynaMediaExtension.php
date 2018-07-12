<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

use Ekyna\Bundle\ResourceBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaMediaExtension
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaMediaExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->configure($configs, 'ekyna_media', new Configuration(), $container);

        $container->setParameter('ekyna_media.watermark', $config['watermark']);
    }
}
