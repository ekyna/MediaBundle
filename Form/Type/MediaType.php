<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Form\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Ekyna\Bundle\UiBundle\Form\Type\UploadType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Form\Type
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends AbstractResourceType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => MediaTranslationType::class,
                'label'     => false,
                'required'  => false,
                'attr'      => [
                    'widget_col' => 12,
                ],
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return UploadType::class; // TODO check (from core ?)
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return 'ekyna_media_media';
    }
}
