<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormStaticControlType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ImportMediaType extends AbstractType
{
    /**
     * @var string
     */
    private $dataClass;


    /**
     * Constructor.
     *
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', FormStaticControlType::class, [
                'label' => 'Fichier',
            ])
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => MediaTranslationType::class,
                'label'     => false,
                'attr'      => [
                    'widget_col' => 12,
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => $this->dataClass,
            ]);
    }
}
