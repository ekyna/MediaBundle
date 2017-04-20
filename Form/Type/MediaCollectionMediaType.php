<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaCollectionMediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaCollectionMediaType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', MediaChoiceType::class, [
                'types'    => $options['types'],
                'controls' => [
                    ['role' => 'show', 'icon' => 'play', 'title' => 'Preview'],
                    ['role' => 'move-left', 'icon' => 'arrow-left', 'title' => 'Move left'],
                    ['role' => 'remove', 'icon' => 'remove', 'title' => 'Remove'],
                    ['role' => 'move-right', 'icon' => 'arrow-right', 'title' => 'Move right'],
                ],
                'gallery'  => true,
            ])
            ->add('position', HiddenType::class, [
                'attr' => [
                    'data-role' => 'position',
                ],
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label'    => false,
                'required' => false,
                'types'    => null,
            ])
            ->setAllowedTypes('types', ['null', 'string', 'array'])
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
    public function getBlockPrefix(): string
    {
        return 'ekyna_media_collection_media';
    }
}
