<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\AdminBundle\Form\Type\ResourceFormType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class UploadMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class UploadMediaType extends ResourceFormType
{
    /**
     * @inheritdoc
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
            ->add('key', Type\HiddenType::class)
            ->add('rename', Type\TextType::class, [
                'label'    => false,
                'required' => true,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_media_upload_media';
    }
}
