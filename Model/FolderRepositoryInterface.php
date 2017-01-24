<?php

namespace Ekyna\Bundle\MediaBundle\Model;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;

/**
 * Interface FolderRepositoryInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface FolderRepositoryInterface extends ResourceRepositoryInterface
{

    /**
     * @inheritdoc
     * @return FolderInterface
     */
    public function createNew();

    /**
     * Finds the root folder.
     *
     * @return null|FolderInterface
     */
    public function findRoot();

    /**
     * Finds the folder by his name and parent.
     *
     * @param string $name
     * @param string|FolderInterface $parentNameOrFolder
     *
     * @return null|FolderInterface
     */
    public function findOneByNameAndParent($name, $parentNameOrFolder = null);
}
