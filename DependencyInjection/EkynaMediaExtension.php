<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

use Ekyna\Bundle\AdminBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaMediaExtension
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaMediaExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->configure($configs, 'ekyna_media', new Configuration(), $container);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

        $bundles = $container->getParameter('kernel.bundles');
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (array_key_exists('AsseticBundle', $bundles)) {
            $this->configureAsseticBundle($container, $config['output_dir']);
        }
        if (array_key_exists('TwigBundle', $bundles)) {
            $this->configureTwigBundle($container);
        }
    }

    /**
     * Configures the AsseticBundle.
     *
     * @param ContainerBuilder $container
     * @param string           $outputDir
     */
    protected function configureAsseticBundle(ContainerBuilder $container, $outputDir)
    {
        $asseticConfig = new AsseticConfiguration();
        $container->prependExtensionConfig('assetic', array(
            'assets' => $asseticConfig->build($outputDir),
            'bundles' => array('EkynaMediaBundle'),
        ));
    }

    /**
     * Configures the TwigBundle.
     *
     * @param ContainerBuilder $container
     */
    protected function configureTwigBundle(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('twig', array(
            'form' => array('resources' => array('EkynaMediaBundle:Form:form_div_layout.html.twig')),
        ));
    }
}
