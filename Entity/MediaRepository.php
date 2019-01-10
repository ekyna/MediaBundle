<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepository;

/**
 * Class MediaRepository
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method \Ekyna\Bundle\MediaBundle\Model\MediaInterface|null find($id, $lockMode = null, $lockVersion = null)
 */
class MediaRepository extends TranslatableResourceRepository
{
    /**
     * @param FolderInterface $folder
     * @param array           $types
     *
     * @return \Ekyna\Bundle\MediaBundle\Model\MediaInterface[]
     */
    public function findByFolderAndTypes(FolderInterface $folder, array $types = [])
    {
        $qb = $this->createQueryBuilder('m');
        $ex = $qb->expr();

        $qb->andWhere($ex->eq('m.folder', ':folder'));
        $parameters = [
            'folder' => $folder,
        ];

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
