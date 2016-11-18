<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model\FolderRepositoryInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Util\ResourceRepositoryTrait;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class FolderRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class FolderRepository extends NestedTreeRepository implements FolderRepositoryInterface
{
    use ResourceRepositoryTrait {
        createNew as traitCreateNew;
    }

    /**
     * @var FolderInterface
     */
    private $root;


    /**
     * @inheritdoc
     */
    public function createNew()
    {
        /** @var FolderInterface $folder */
        $folder = $this->traitCreateNew();
        $folder->setParent($this->findRoot());

        return $folder;
    }

    /**
     * @inheritdoc
     */
    public function findRoot()
    {
        if (null !== $this->root) {
            return $this->root;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->root = $this->findOneBy([
            'name'  => FolderInterface::ROOT,
            'level' => 0,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function findOneByNameAndParent($name, $parentNameOrFolder = null)
    {
        $parent = null;
        if (null === $parentNameOrFolder) {
            $parent = $this->findRoot();
        } elseif ($parentNameOrFolder instanceof FolderInterface) {
            $parent = $parentNameOrFolder;
        } elseif (0 < strlen($parentNameOrFolder)) {
            $parent = $this->findOneByNameAndParent($parentNameOrFolder);
        }

        if (null === $parent) {
            throw new \InvalidArgumentException("Failed to retrieve the parent folder.");
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->findOneBy([
            'name'   => $name,
            'parent' => $parent,
        ]);
    }
}
