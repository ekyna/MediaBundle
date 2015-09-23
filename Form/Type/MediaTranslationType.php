<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaTranslationType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaTranslationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'ekyna_core.field.title',
                'required' => false,
            ])
            /*->add('description', 'tinymce', array(
                'label' => 'ekyna_core.field.description',
                'theme' => 'advanced',
                'required' => false,
            ))*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'Ekyna\Bundle\MediaBundle\Entity\MediaTranslation',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_media_translation';
    }
}
