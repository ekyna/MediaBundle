<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UploadMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class UploadMediaType extends AbstractType
{
    private string $dataClass;

    public function __construct(string $dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', $this->dataClass);
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_media_upload_media';
    }
}
