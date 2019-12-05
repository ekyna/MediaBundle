<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\CoreBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaChoiceType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaChoiceType extends AbstractType
{
    /**
     * @var EntityRepository
     */
    protected $repository;


    /**
     * Constructor.
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdentifierTransformer($this->repository);
        $builder->addViewTransformer($transformer);

        // TODO Constraint against types
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['media'] = $form->getNormData();
        $view->vars['config'] = [
            'types'    => (array)$options['types'],
            'controls' => $options['controls'],
        ];
        $view->vars['gallery'] = $options['gallery'];
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'label'          => 'ekyna_media.media.label.singular',
                'types'          => null,
                'error_bubbling' => false,
                'controls'       => [
                    ['role' => 'show', 'icon' => 'play', 'title' => 'Preview'],
                    ['role' => 'download', 'icon' => 'download', 'title' => 'Download'],
                    ['role' => 'remove', 'icon' => 'remove', 'title' => 'Remove'],
                ],
                'gallery'        => false,
            ])
            ->setAllowedTypes('types', ['null', 'string', 'array'])
            ->setAllowedTypes('controls', 'array')
            ->setAllowedTypes('gallery', 'bool')
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
     * @inheritdoc
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'ekyna_media_choice';
    }
}
