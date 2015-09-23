<?php

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Craue\FormFlowBundle\Form\FormFlow;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MediaImportFlow
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImportFlow extends FormFlow
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;


    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator  = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'selection',
                'type'  => 'ekyna_media_import_selection',
            ],
            [
                'label' => 'creation',
                'type'  => 'ekyna_media_import_creation',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormOptions($step, array $options = [])
    {
        $options = parent::getFormOptions($step, $options);

        /** @var \Ekyna\Bundle\MediaBundle\Model\Import\MediaImport $import */
        if (null === $import = $this->getFormData()) {
            throw new \Exception('Initial import object must be set.');
        }

        $options['validation_groups'] = ['Default']; //, $step == 1 ? 'Selection' : 'Creation'
        $options['action'] = $this->urlGenerator->generate(
            'ekyna_media_browser_admin_import_media',
            ['id' => $import->getFolder()->getId()]
        );
        $options['method'] = 'post';
        $options['admin_mode'] = true;
        $options['attr'] = [
            'class' => 'form form-horizontal form-with-tabs',
        ];

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'import_media';
    }
}
