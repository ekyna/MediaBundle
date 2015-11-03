<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UploadMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class UploadMediaType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', array(
                'form_type' => new MediaTranslationType(),
                'label'     => false,
                'required'  => false,
                'attr'      => array(
                    'widget_col' => 12,
                ),
            ))
            ->add('key', 'hidden')
            ->add('rename', 'text', array(
                'label'    => false,
                'required' => true,
                'sizing'   => 'sm',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_upload_media';
    }
}
