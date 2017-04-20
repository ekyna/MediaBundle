<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\UiBundle\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function is_array;
use function is_string;

/**
 * Class MediaCollectionType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaCollectionType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['config'] = [
            'types' => (array)$options['types'],
            'limit' => $options['limit'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'media_class'   => null,
                'types'         => null,
                'limit'         => 0,
                'allow_add'     => true,
                'allow_delete'  => true,
                'allow_sort'    => true,
                'entry_type'    => MediaCollectionMediaType::class,
                'entry_options' => function (Options $options) {
                    return [
                        'label'      => false,
                        'types'      => $options['types'],
                        'data_class' => $options['media_class'],
                    ];
                },
            ])
            ->setAllowedTypes('media_class', 'string')
            ->setAllowedTypes('types', ['null', 'string', 'array'])
            ->setAllowedTypes('limit', 'int')
            ->setAllowedValues('types', function ($value) {
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
            });
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return CollectionType::class;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return 'ekyna_media_collection';
    }
}
