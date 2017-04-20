<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use Craue\FormFlowBundle\Form\FormFlow;
use Ekyna\Bundle\MediaBundle\Form\Type\Step\MediaImportCreationType;
use Ekyna\Bundle\MediaBundle\Form\Type\Step\MediaImportSelectionType;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaImport;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MediaImportFlow
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaImportFlow extends FormFlow
{
    private UrlGeneratorInterface $urlGenerator;


    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    protected function loadStepsConfig(): array
    {
        return [
            [
                'label'     => 'selection',
                'form_type' => MediaImportCreationType::class,
            ],
            [
                'label'     => 'creation',
                'form_type' => MediaImportSelectionType::class,
            ],
        ];
    }

    /**
     * @param       $step
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    public function getFormOptions($step, array $options = []): array
    {
        $options = parent::getFormOptions($step, $options);

        /** @var MediaImport $import */
        if (null === $import = $this->getFormData()) {
            throw new Exception('Initial import object must be set.');
        }

        $options['validation_groups'] = ['Default']; //, $step == 1 ? 'Selection' : 'Creation'
        $options['action'] = $this->urlGenerator->generate(
            'admin_ekyna_media_browser_import_media',
            ['id' => $import->getFolder()->getId()]
        );
        $options['method'] = 'post';
        //$options['admin_mode'] = true;
        $options['attr'] = [
            'class' => 'form form-horizontal form-with-tabs',
        ];

        return $options;
    }
}
