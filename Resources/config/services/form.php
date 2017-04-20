<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\MediaBundle\Form\Type\ImportMediaType;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaChoiceType;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaCollectionMediaType;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaCollectionType;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaImportFlow;
use Ekyna\Bundle\MediaBundle\Form\Type\Step\MediaImportCreationType;
use Ekyna\Bundle\MediaBundle\Form\Type\Step\MediaImportSelectionType;
use Ekyna\Bundle\MediaBundle\Form\Type\UploadMediaType;
use Ekyna\Bundle\MediaBundle\Form\Type\UploadType;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Media choice form type
        ->set('ekyna_media.form_type.media_choice', MediaChoiceType::class)
            ->args([
                service('ekyna_media.repository.media'),
            ])
            ->tag('form.type')
            ->tag('form.js', ['selector' => '.ekyna-media-choice', 'path' => 'ekyna-media/form/choice'])

        // Media collection form type
        ->set('ekyna_media.form_type.media_collection', MediaCollectionType::class)
            ->tag('form.type')
            ->tag('form.js', ['selector' => '.ekyna-media-collection', 'path' => 'ekyna-media/form/collection'])

        // Media collection media form type
        ->set('ekyna_media.form_type.media_collection_media', MediaCollectionMediaType::class)
            ->tag('form.type')

        // Upload form type
        ->set('ekyna_media.form_type.upload', UploadType::class)
            ->tag('form.type')
            ->tag('form.js', ['selector' => '.ekyna-media-upload', 'path' => 'ekyna-media/form/upload'])

        // Upload media form type
        ->set('ekyna_media.form_type.upload_media', UploadMediaType::class)
            ->args([
                param('ekyna_media.class.media')
            ])
            ->tag('form.type')

        // Import media form type
        ->set('ekyna_media.form_type.import', ImportMediaType::class)
            ->args([
                param('ekyna_media.class.media')
            ])
            ->tag('form.type')

        // Media import form flow
        ->set('ekyna_media.form_flow.media_import', MediaImportFlow::class)
            ->parent('craue.form.flow')
            ->args([
                service('router'),
            ])

        // Media import selection form type
        ->set('ekyna_media.form_type.media_import_selection', MediaImportSelectionType::class)
            ->args([
                service('ekyna_media.filesystem.media'),
            ])
            ->tag('form.type')

        // Media import creation form type
        ->set('ekyna_media.form_type.media_import_creation', MediaImportCreationType::class)
            ->args([
                service('ekyna_media.factory.media'),
            ])
            ->tag('form.type')
    ;
};
