<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Ekyna\Bundle\CoreBundle\Form\Type\UploadType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends ResourceFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => MediaTranslationType::class,
                'label'     => false,
                'required'  => false,
                'attr'      => [
                    'widget_col' => 12,
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return UploadType::class; // TODO check (from core ?)
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'ekyna_media_media';
    }
}
