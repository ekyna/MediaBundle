<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\CoreBundle\Form\Type\CollectionType;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaUpload;
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
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                /** @var \Ekyna\Bundle\MediaBundle\Model\Import\MediaUpload $data */
                foreach ($data->getMedias() as $media) {
                    $media->setFolder($options['folder']);
                }

                return $data;
            }
        ));
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => MediaUpload::class,
                'folder'     => null,
            ])
            ->setAllowedTypes('folder', FolderInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_media_upload';
    }
}
