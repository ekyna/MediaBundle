<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Ekyna\Bundle\CoreBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * @param EntityRepository    $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ObjectToIdentifierTransformer($this->repository);
        $builder->addViewTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['media'] = $form->getData();
        $view->vars['config'] = array(
            'types'    => (array)$options['types'],
            'controls' => $options['controls'],
        );
        $view->vars['gallery'] = $options['gallery'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'label'          => 'ekyna_media.media.label.singular',
                'types'          => null,
                'error_bubbling' => false,
                'controls'       => array(
                    array('role' => 'remove', 'icon' => 'remove'),
                ),
                'gallery'        => false,
            ))
            ->setAllowedTypes(array(
                'types'    => array('null', 'string', 'array'),
                'controls' => 'array',
                'gallery'  => 'bool',
            ))
            ->setAllowedValues(array(
                'types' => function ($value) {
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
                },
            ));;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media_choice';
    }
}
