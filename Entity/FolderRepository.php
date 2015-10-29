<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepositoryInterface;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\Util\ResourceRepositoryTrait;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class FolderRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FolderRepository extends NestedTreeRepository implements ResourceRepositoryInterface
{
    use ResourceRepositoryTrait {
        createNew as traitCreateNew;
    }

    /**
     * {@inheritdoc}
     * @return FolderInterface
     */
    public function createNew()
    {
        /** @var FolderInterface $folder */
        $folder = $this->traitCreateNew();
        $folder->setParent($this->findRoot());
        return $folder;
    }

    /**
     * Finds the root folder.
     *
     * @return null|Folder
     */
    public function findRoot()
    {
        return $this->findOneBy([
            'name' => FolderInterface::ROOT,
            'level' => 0,
        ]);
    }
}
