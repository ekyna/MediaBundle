<?php

namespace Ekyna\Bundle\MediaBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ekyna\Bundle\MediaBundle\Entity\ConversionRequest;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class ConversionRequestRepository
 * @package Ekyna\Bundle\MediaBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ConversionRequestRepository extends ServiceEntityRepository
{
    /**
     * @inheritDoc
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversionRequest::class);
    }

    /**
     * Finds the running request.
     *
     * @return ConversionRequest|null
     */
    public function findRunning(): ?ConversionRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->findOneBy([
            'state' => ConversionRequest::STATE_RUNNING,
        ]);
    }

    /**
     * Finds the next request to run.
     *
     * @return ConversionRequest|null
     */
    public function findNext(): ?ConversionRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->findOneBy([
            'state' => ConversionRequest::STATE_PENDING,
        ], [
            'createdAt' => 'ASC',
        ]);
    }

    /**
     * Finds requests by media.
     *
     * @param MediaInterface $media
     *
     * @return ConversionRequest[]
     */
    public function findByMedia(MediaInterface $media): array
    {
        return $this->findBy([
            'media' => $media,
        ]);
    }
}
