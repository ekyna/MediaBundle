<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class FolderRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class FolderRepository extends NestedTreeRepository
{
    /**
     * Finds the root folder by his name.
     *
     * @param string $name
     * @return null|Folder
     */
    public function findRootByName($name)
    {
        return $this->findOneBy(array(
            'name' => $name,
            'level' => 0,
        ));
    }
}
