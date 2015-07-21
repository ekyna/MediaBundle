<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MediaCollectionMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaCollectionMediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', 'ekyna_media_choice', array(
                'types' => $options['types'],
                'controls' => array(
                    array('role' => 'move-left', 'icon' => 'arrow-left'),
                    array('role' => 'remove', 'icon' => 'remove'),
                    array('role' => 'move-right', 'icon' => 'arrow-right'),
                ),
            ))
            ->add('position', 'hidden', array(
                'attr' => array(
                    'data-role' => 'position'
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'label'    => false,
                'required' => false,
                'types'    => null,
            ))
            ->setAllowedTypes(array(
                'types' => array('null', 'string', 'array'),
            ))
            ->setAllowedValues(array(
                'types' => function($value) {
                    if (is_string($value)) {
                        return MediaTypes::isValid($value);
                    } elseif (is_array($value)) {
                        foreach ($value as $v) {
                            if (!MediaTypes::isValid($v)) {
                                return false;
                            }
                        }
                    }
                    return true;
                }
            ));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_collection_media';
    }
}
