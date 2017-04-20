<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\ResourceBundle\Form\DataTransformer\IdentifierToResourceTransformer;
use Ekyna\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class MediaChoiceType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaChoiceType extends AbstractType
{
    protected MediaRepositoryInterface $repository;


    public function __construct(MediaRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['transform']) {
            $builder->addModelTransformer(new IdentifierToResourceTransformer($this->repository));
        }

        $builder->addViewTransformer(new ResourceToIdentifierTransformer($this->repository));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['media'] = $form->getNormData();
        $view->vars['config'] = [
            'types'    => (array)$options['types'],
            'controls' => $options['controls'],
        ];
        $view->vars['gallery'] = $options['gallery'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label'          => t('media.label.singular', [], 'EkynaMedia'),
                'types'          => null,
                'error_bubbling' => false,
                'controls'       => [
                    ['role' => 'show', 'icon' => 'play', 'title' => 'Preview'],
                    ['role' => 'download', 'icon' => 'download', 'title' => 'Download'],
                    ['role' => 'remove', 'icon' => 'remove', 'title' => 'Remove'],
                ],
                'gallery'        => false,
                'transform'      => false,
            ])
            ->setAllowedTypes('types', ['null', 'string', 'array'])
            ->setAllowedTypes('controls', 'array')
            ->setAllowedTypes('gallery', 'bool')
            ->setAllowedTypes('transform', 'bool')
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

    public function getParent(): ?string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ekyna_media_choice';
    }
}
