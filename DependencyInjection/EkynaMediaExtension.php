<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

use Ekyna\Bundle\MediaBundle\Service\SchemaOrg\MediaProvider;
use Ekyna\Bundle\ResourceBundle\DependencyInjection\PrependBundleConfigTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

use function in_array;

/**
 * Class EkynaMediaExtension
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaMediaExtension extends Extension implements PrependExtensionInterface
{
    use PrependBundleConfigTrait;

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        foreach (['jpeg', 'png'] as $extension) {
            $container->setParameter(
                'ekyna_media.image.quality.' . $extension,
                $config['image']['quality'][$extension]
            );
        }

        $container->setParameter('ekyna_media.video.directory', $config['video']['directory']);

        $this->prependBundleConfigFiles($container);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services/console.php');
        $loader->load('services/controller.php');
        $loader->load('services/ffmpeg.php');
        $loader->load('services/form.php');
        $loader->load('services/listener.php');
        $loader->load('services/message.php');
        $loader->load('services/show.php');
        $loader->load('services/twig.php');
        $loader->load('services.php');

        if (in_array($container->getParameter('kernel.environment'), ['dev', 'test'], true)) {
            $loader->load('services/dev.php');
        }

        $this->configureVideo($config['video'], $container);
        $this->configureFFMpeg($config['ffmpeg'], $container);
        $this->configureSchemaOrg($container);
    }

    private function configureVideo(array $config, ContainerBuilder $container): void
    {
        $container
            ->getDefinition('ekyna_media.manager.video')
            ->replaceArgument(6, $config);
    }

    private function configureFFMpeg(array $config, ContainerBuilder $container): void
    {
        $container
            ->getDefinition('ekyna_media.ffmpeg')
            ->replaceArgument(0, [
                'ffmpeg.binaries' => [$config['ffmpeg_binary']],
                'timeout'          => $config['binary_timeout'],
                'ffmpeg.threads'   => $config['threads_count'],
            ]);

        $container
            ->getDefinition('ekyna_media.ffprobe')
            ->replaceArgument(0, [
                'ffprobe.binaries' => [$config['ffprobe_binary']],
                'timeout'          => $config['binary_timeout'],
            ]);
    }

    private function configureSchemaOrg(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        if (in_array('EkynaCmsBundle', $bundles, true)) {
            $container
                ->register('ekyna_media.schema_org.media_provider', MediaProvider::class)
                ->setArguments([
                    new Reference('ekyna_media.generator'),
                ])
                ->addTag('ekyna_cms.schema_org_provider');
        }
    }
}
