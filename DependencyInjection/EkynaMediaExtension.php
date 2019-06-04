<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

use Ekyna\Bundle\ResourceBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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

        if (in_array($container->getParameter('kernel.environment'), ['dev', 'test'], true)) {
            $loader = new XmlFileLoader($container, new FileLocator($this->getConfigurationDirectory()));
            $loader->load('services_dev_test.xml');
        }
    }
}
