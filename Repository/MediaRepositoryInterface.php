<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Repository;

use Ekyna\Bundle\MediaBundle\Model;
use Ekyna\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Interface MediaRepositoryInterface
 * @package Ekyna\Bundle\MediaBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Model\MediaInterface|null find(int $id)
 * @method Model\MediaInterface|null findOneBy(array $criteria, array $sorting = [])
 * @method Model\MediaInterface[] findAll()
 * @method Model\MediaInterface[] findBy(array $criteria, array $sorting = [], int $limit = null, int $offset = null)
 */
interface MediaRepositoryInterface extends TranslatableRepositoryInterface
{
    /**
     * Finds one media by its path.
     *
     * @param string $path
     *
     * @return Model\MediaInterface|null
     */
    public function findOneByPath(string $path): ?Model\MediaInterface;

    /**
     * Finds media by folder and types.
     *
     * @param Model\FolderInterface $folder
     * @param array           $types
     *
     * @return Model\MediaInterface[]
     */
    public function findByFolderAndTypes(Model\FolderInterface $folder, array $types = []): array;
}
