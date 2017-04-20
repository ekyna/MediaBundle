<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Repository;

use Ekyna\Bundle\MediaBundle\Model;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\TranslatableRepository;

/**
 * Class MediaRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Model\MediaInterface|null find(int $id)
 * @method Model\MediaInterface|null findOneBy(array $criteria, array $sorting = [])
 * @method Model\MediaInterface[] findAll()
 * @method Model\MediaInterface[] findBy(array $criteria, array $sorting = [], int $limit = null, int $offset = null)
 */
class MediaRepository extends TranslatableRepository implements MediaRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findOneByPath(string $path): ?Model\MediaInterface
    {
        $qb = $this->createQueryBuilder('m');
        $ex = $qb->expr();

        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb
            ->andWhere($ex->eq('m.path', ':path'))
            ->getQuery()
            ->setParameter('path', $path)
            ->getOneOrNullResult();
    }

    /**
     * @inheritDoc
     */
    public function findByFolderAndTypes(Model\FolderInterface $folder, array $types = []): array
    {
        $qb = $this->createQueryBuilder('m');
        $ex = $qb->expr();

        $qb->andWhere($ex->eq('m.folder', ':folder'));
        $parameters = ['folder' => $folder];

        if (!empty($types)) {
            $qb->andWhere($ex->in('m.type', ':types'));
            $parameters['types'] = $types;
        }

        return $qb
            ->getQuery()
            ->setParameters($parameters)
            ->getResult();
    }
}
