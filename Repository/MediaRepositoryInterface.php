<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Repository;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;
use Ekyna\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Interface MediaRepositoryInterface
 * @package Ekyna\Bundle\MediaBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @implements ResourceRepositoryInterface<MediaInterface>
 */
interface MediaRepositoryInterface extends TranslatableRepositoryInterface
{
    /**
     * Finds one media by its path.
     */
    public function findOneByPath(string $path): ?MediaInterface;

    /**
     * Finds media by folder and types.
     *
     * @return array<MediaInterface>
     */
    public function findByFolderAndTypes(FolderInterface $folder, array $types = []): array;
}
