<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ImageType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['alt_field']) {
            $builder->add('alt', 'text', array(
                'label'        => 'ekyna_core.field.alt',
                'required'     => false,
                'sizing'       => 'sm',
                'admin_helper' => 'IMAGE_ALT',
                'attr'         => array(
                    'label_col'  => 2,
                    'widget_col' => 10
                ),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['thumb_col'] = $options['thumb_col'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'label'      => 'ekyna_core.field.image',
                'thumb_col'  => 3,
                'alt_field'  => true,
            ))
            ->setRequired(array('data_class'))
            ->setAllowedTypes(array(
                'thumb_col'  => 'int',
                'alt_field'  => 'bool',
            ))
            ->setNormalizers(array(
                'thumb_col' => function($options, $value) {
                    if (0 == strlen($options['file_path'])) {
                        return 0;
                    }
                    if ($value > 4) {
                        return 4;
                    }
                    return $value;
                },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_media_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_image';
    }
}
