<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaCollectionType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['config'] = [
            'types' => (array) $options['types'],
            'limit' => $options['limit'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'media_class'  => null,
                'types'        => null,
                'limit'        => 0,
                'allow_add'    => true,
                'allow_delete' => true,
                'allow_sort'   => true,
                'type'         => 'ekyna_media_collection_media',
                'options'      => function(Options $options) {
                    return [
                        'label'      => false,
                        'types'      => $options['types'],
                        'data_class' => $options['media_class'],
                    ];
                },
            ])
            ->setAllowedTypes('media_class', 'string')
            ->setAllowedTypes('types',       ['null', 'string', 'array'])
            ->setAllowedTypes('limit',       'int')
            ->setAllowedValues([
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
            ]);
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ekyna_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_collection';
    }
}
