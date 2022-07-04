<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Action\Admin\MediaListAction;
use Ekyna\Bundle\MediaBundle\Factory\FolderFactory;
use Ekyna\Bundle\MediaBundle\Install\MediaInstaller;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Ekyna\Bundle\MediaBundle\Service\Serializer\MediaNormalizer;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Ekyna\Bundle\MediaBundle\Validator\Constraints\MediaValidator;
use Ekyna\Bundle\ResourceBundle\DependencyInjection\Compiler\UploaderPass;
use Ekyna\Bundle\ResourceBundle\Service\Uploader\Uploader;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Media list action
        ->set('ekyna_media.action.media_list', MediaListAction::class)
            ->tag('ekyna_resource.action')

        // Folder factory
        ->set('ekyna_media.factory.folder', FolderFactory::class)
            ->args([
                service('ekyna_media.repository.folder'),
            ])

        // Uploader
        ->set('ekyna_media.uploader', Uploader::class)
            ->args([
                service('ekyna_resource.filesystem.tmp'),
                service('ekyna_media.filesystem.media'),
            ])
            ->tag(UploaderPass::UPLOADER_TAG)

        // Video manager
        ->set('ekyna_media.manager.video', VideoManager::class)
            ->args([
                service('ekyna_media.filesystem.media'),
                service('ekyna_media.filesystem.video'),
                service('ekyna_media.ffmpeg'),
                service('ekyna_media.ffprobe'),
                service('liip_imagine.cache.manager'),
                service('router'),
                abstract_arg('Video configuration'),
            ])

        // Media generator
        ->set('ekyna_media.generator', Generator::class)
            ->args([
                service('ekyna_media.filesystem.media'),
                service('liip_imagine'),
                service('liip_imagine.cache.manager'),
                service('ekyna_media.manager.video'),
                service('router'),
                param('kernel.project_dir') . '/public',
                'cache/media/media_thumb'
            ])
            ->tag('twig.runtime')

        // Media renderer
        ->set('ekyna_media.renderer', Renderer::class)
            ->args([
                service('twig'),
                service('ekyna_media.generator'),
                service('ekyna_media.manager.video'),
                service('liip_imagine.filter.manager'),
                service('ekyna_media.repository.media'),
                service('translator'),
            ])
            ->tag('twig.runtime')

        // Media validator
        ->set('ekyna_media.validator.media', MediaValidator::class)
            ->args([
                service('ekyna_resource.filesystem.tmp'),
            ])
            ->tag('validator.constraint_validator')

        // Media installer
        ->set('ekyna_media.installer', MediaInstaller::class)
            ->args([
                service('ekyna_media.repository.folder'),
                service('ekyna_media.manager.folder'),
                service('ekyna_media.factory.folder'),
            ])
            ->tag('ekyna_install.installer', ['priority' => 99]) // TODO ?

        // Serializer
        ->set('ekyna_media.normalizer.media', MediaNormalizer::class)
            ->args([
                service('ekyna_media.generator'),
            ])

        // Filesystems aliases
        ->alias('ekyna_media.filesystem.media', 'oneup_flysystem.local_media_filesystem')
        ->alias('ekyna_media.filesystem.video', 'oneup_flysystem.local_video_filesystem')
    ;
};
