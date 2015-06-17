<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class GalleryMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryMediaType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', 'hidden', array(
                'attr' => array(
                    'data-collection-role' => 'position'
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_media_media';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_gallery_media';
    }
}
