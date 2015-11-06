<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UploadType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class UploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('medias', 'ekyna_collection', array(
            'label'          => false,
            'sub_widget_col' => 11,
            'button_col'     => 1,
            'type'           => 'ekyna_media_upload_media',
        ));

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
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Ekyna\Bundle\MediaBundle\Model\Import\MediaUpload',
                'folder'     => null,
            ))
            ->setAllowedTypes(array(
                'folder' => 'Ekyna\Bundle\MediaBundle\Model\FolderInterface',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_upload';
    }
}
