<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Repository;

use Ekyna\Bundle\MediaBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\ResourceRepositoryTrait;

/**
 * Class FolderRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method FolderInterface|null find(int $id)
 * @method FolderInterface|null findOneBy(array $criteria, array $sorting = null)
 * @method FolderInterface[] findBy(array $criteria, array $sorting = null, int $limit = null, int $offset = null)
 * @method FolderInterface[] findAll()
 * @method persistAsFirstChild($node)
 * @method persistAsFirstChildOf($node, $parent)
 * @method persistAsLastChild($node)
 * @method persistAsLastChildOf($node, $parent)
 * @method persistAsNextSibling($node)
 * @method persistAsNextSiblingOf($node, $sibling)
 * @method persistAsPrevSibling($node)
 * @method persistAsPrevSiblingOf($node, $sibling)
 */
class FolderRepository implements FolderRepositoryInterface
{
    use ResourceRepositoryTrait;

    private ?FolderInterface $root = null;


    /**
     * @inheritDoc
     */
    public function findRoot(): ?FolderInterface
    {
        if (null !== $this->root) {
            return $this->root;
        }

        return $this->root = $this->findOneBy([
            'name'  => FolderInterface::ROOT,
            'level' => 0,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function findOneByNameAndParent(string $name, $parentNameOrFolder = null): ?FolderInterface
    {
        $parent = null;
        if (null === $parentNameOrFolder) {
            $parent = $this->findRoot();
        } elseif ($parentNameOrFolder instanceof FolderInterface) {
            $parent = $parentNameOrFolder;
        } elseif (!empty($parentNameOrFolder)) {
            $parent = $this->findOneByNameAndParent($parentNameOrFolder);
        }

        if (null === $parent) {
            throw new InvalidArgumentException('Failed to retrieve the parent folder.');
        }

        return $this->findOneBy([
            'name'   => $name,
            'parent' => $parent,
        ]);
    }
}
