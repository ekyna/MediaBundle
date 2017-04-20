<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Factory;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Repository\FolderRepositoryInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Factory\ResourceFactory;
use Ekyna\Component\Resource\Model\ResourceInterface;

/**
 * Class FolderFactory
 * @package Ekyna\Bundle\MediaBundle\Factory
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class FolderFactory extends ResourceFactory implements FolderFactoryInterface
{
    private FolderRepositoryInterface $repository;

    /**
     * Constructor.
     *
     * @param FolderRepositoryInterface $repository
     */
    public function __construct(FolderRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function create(): ResourceInterface
    {
        /** @var FolderInterface $folder */
        $folder = parent::create();

        $folder->setParent($this->repository->findRoot());

        return $folder;
    }
}
