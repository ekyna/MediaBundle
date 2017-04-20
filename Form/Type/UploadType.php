<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaUpload;
use Ekyna\Bundle\UiBundle\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UploadType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class UploadType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('medias', CollectionType::class, [
            'label'          => false,
            'sub_widget_col' => 11,
            'button_col'     => 1,
            'entry_type'     => UploadMediaType::class,
        ]);

        $builder->addModelTransformer(new CallbackTransformer(
            function ($data) {
                return $data;
            },
            // Sets the folder (form view => model)
            function ($data) use ($options) {
                /** @var MediaUpload $data */
                foreach ($data->getMedias() as $media) {
                    $media->setFolder($options['folder']);
                }

                return $data;
            }
        ));
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['folder'])
            ->setDefault('data_class', MediaUpload::class)
            ->setAllowedTypes('folder', FolderInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return 'ekyna_media_upload';
    }
}
