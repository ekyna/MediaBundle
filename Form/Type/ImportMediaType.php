<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ImportMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
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
            ->add('key', 'bs_static', [
                'label' => 'Fichier',
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new MediaTranslationType(),
                'label'     => false,
                'attr' => [
                    'widget_col' => 12,
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => $this->dataClass
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_import';
    }
}
