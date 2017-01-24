<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type\Step;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Form\Type\ImportMediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class MediaImportCreationType
 * @package Ekyna\Bundle\MediaBundle\Form\Type\Step
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImportCreationType extends AbstractType
{
    /**
     * @var MediaRepository
     */
    private $mediaRepository;


    /**
     * Constructor.
     *
     * @param MediaRepository $mediaRepository
     */
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $repo = $this->mediaRepository;
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($repo) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\Import\MediaImport $import */
            if (null === $import = $event->getData()) {
                throw new \Exception('Initial import object must be set.');
            }
            if (0 == count($import->getKeys())) {
                throw new \Exception('At this point the "keys" property must not be empty.');
            }

            $form = $event->getForm();

            foreach ($import->getKeys() as $key) {
                /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
                $media = $repo->createNew();
                $media
                    ->setKey($key)
                    ->setRename(pathinfo($key, PATHINFO_BASENAME))
                ;
                $import->addMedia($media);
            }

            $form->add('medias', CollectionType::class, [
                'label' => false,
                'entry_type' => ImportMediaType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'attr' => [
                    'widget_col' => 12,
                ],
            ]);
        });
    }
}
