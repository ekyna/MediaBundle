<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Repository;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Interface FolderRepositoryInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @implements ResourceRepositoryInterface<FolderInterface>
 *
 * @method persistAsFirstChild($node)
 * @method persistAsFirstChildOf($node, $parent)
 * @method persistAsLastChild($node)
 * @method persistAsLastChildOf($node, $parent)
 * @method persistAsNextSibling($node)
 * @method persistAsNextSiblingOf($node, $sibling)
 * @method persistAsPrevSibling($node)
 * @method persistAsPrevSiblingOf($node, $sibling)
 */
interface FolderRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Finds the root folder.
     */
    public function findRoot(): ?FolderInterface;

    /**
     * Finds the folder by his name and parent.
     *
     * @param string|FolderInterface $parentNameOrFolder
     */
    public function findOneByNameAndParent(string $name, $parentNameOrFolder = null): ?FolderInterface;
}
