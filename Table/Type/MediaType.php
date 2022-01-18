<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Action;
use Ekyna\Bundle\ResourceBundle\Table\Type\AbstractResourceType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type as CType;
use Ekyna\Component\Table\TableBuilderInterface;

use function Symfony\Component\Translation\t;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends AbstractResourceType
{
    public function buildTable(TableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addColumn('title', BType\Column\AnchorType::class, [
                'label'    => t('field.title', [], 'EkynaUi'),
                'sortable' => true,
                'position' => 10,
            ])
            ->addColumn('updatedAt', CType\Column\DateTimeType::class, [
                'label'    => t('field.updated_at', [], 'EkynaUi'),
                'sortable' => true,
                'position' => 20,
            ])
            ->addColumn('actions', BType\Column\ActionsType::class, [
                'resource' => $this->dataClass,
                'actions'  => [
                    Action\UpdateAction::class,
                    Action\DeleteAction::class,
                ],
            ]);
    }
}
