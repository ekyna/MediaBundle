<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class FolderRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method persistAsPrevSiblingOf()
 * @method persistAsNextSiblingOf()
 * @method persistAsFirstChildOf()
 * @method persistAsLastChildOf()
 */
class FolderRepository extends NestedTreeRepository
{
    /**
     * Finds the root folder.
     *
     * @return null|Folder
     */
    public function findRoot()
    {
        return $this->findOneBy(array(
            'name' => FolderInterface::ROOT,
            'level' => 0,
        ));
    }
}
