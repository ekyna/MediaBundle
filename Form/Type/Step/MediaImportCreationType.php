<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type\Step;

use Ekyna\Bundle\MediaBundle\Factory\MediaFactoryInterface;
use Ekyna\Bundle\MediaBundle\Form\Type\ImportMediaType;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaImport;
use Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaImportCreationType
 * @package Ekyna\Bundle\MediaBundle\Form\Type\Step
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImportCreationType extends AbstractType
{
    private MediaFactoryInterface $mediaFactory;


    /**
     * Constructor.
     *
     * @param MediaFactoryInterface $mediaFactory
     */
    public function __construct(MediaFactoryInterface $mediaFactory)
    {
        $this->mediaFactory = $mediaFactory;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /** @var MediaImport $import */
            if (null === $import = $event->getData()) {
                throw new Exception('Initial import object must be set.');
            }

            if (0 === count($import->getKeys())) {
                throw new Exception('At this point the "keys" property must not be empty.');
            }

            $form = $event->getForm();

            foreach ($import->getKeys() as $key) {
                $media = $this->mediaFactory->create();
                $media
                    ->setKey($key)
                    ->setRename(pathinfo($key, PATHINFO_BASENAME));

                $import->addMedia($media);
            }

            $form->add('medias', CollectionType::class, [
                'label'         => false,
                'entry_type'    => ImportMediaType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'attr'          => [
                    'widget_col' => 12,
                ],
            ]);
        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', MediaImport::class);
    }
}
