<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type\Step;

use Ekyna\Bundle\MediaBundle\Model\Import\MediaImport;
use League\Flysystem\MountManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class MediaImportSelectionType
 * @package Ekyna\Bundle\MediaBundle\Form\Type\Step
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImportSelectionType extends AbstractType
{
    /**
     * @var MountManager
     */
    private $mountManager;


    /**
     * Constructor.
     *
     * @param MountManager $mountManager
     */
    public function __construct(MountManager $mountManager)
    {
        $this->mountManager = $mountManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = $this;
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($type) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\Import\MediaImport $import */
            if (null === $import = $event->getData()) {
                throw new \Exception('Initial import object must be set.');
            }

            $form = $event->getForm();

            $form->add('keys', ChoiceType::class, [
                'label' => 'Choisissez des fichiers à importer.',
                'choices' => $type->buildKeysChoices($import),
                'expanded' => true,
                'multiple' => true,
            ]);
        });
    }

    /**
     * Builds the keys choices.
     *
     * @param MediaImport $import
     * @return array
     */
    public function buildKeysChoices(MediaImport $import)
    {
        $prefix = $import->getFilesystem();
        $fs = $this->mountManager->getFilesystem($prefix);
        $contents = $fs->listContents('', true);
        $choices = [];
        foreach ($contents as $object) {
            if (!($object['type'] == 'dir' || substr($object['path'], 0, 1) == '.')) {
                $key = sprintf('%s://%s', $prefix, $object['path']);
                $choices[$key] = $object['path'];
            }
        }
        return $choices;
    }
}
