<?php

namespace Ekyna\Bundle\MediaBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\MediaBundle\Entity\ConversionRequest;
use Ekyna\Bundle\MediaBundle\Repository\ConversionRequestRepository;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class ConversionRequestCommand
 * @package Ekyna\Bundle\MediaBundle\Command
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ConversionRequestCommand extends Command
{
    protected static $defaultName = 'ekyna:media:conversion_request';

    /**
     * @var ConversionRequestRepository
     */
    private $repository;

    /**
     * @var VideoManager
     */
    private $converter;

    /**
     * @var EntityManagerInterface
     */
    private $manager;


    /**
     * Constructor.
     *
     * @param ConversionRequestRepository $repository
     * @param VideoManager                $converter
     * @param EntityManagerInterface      $manager
     */
    public function __construct(
        ConversionRequestRepository $repository,
        VideoManager $converter,
        EntityManagerInterface $manager
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->converter  = $converter;
        $this->manager    = $manager;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Abort if another request is running
        if ($this->repository->findRunning()) {
            return;
        }

        // Abort if no other request to run
        if (!$request = $this->repository->findNext()) {
            return;
        }

        set_time_limit(5 * 60);

        // Set request as running
        $request->setState(ConversionRequest::STATE_RUNNING);
        $this->manager->persist($request);
        $this->manager->flush();

        // Do conversion
        $media = $request->getMedia();
        $format = $request->getFormat();

        try {
            $this->converter->convertVideo($media, $format);
            $success = true;
        } catch (Throwable $t) {
            $success = false;
        }

        // If request has been deleted, abort
        if (!$request = $this->repository->find($request->getId())) {
            // The media has been deleted -> clear converted video
            @unlink($this->converter->getConvertedPath($media, $format));

            return;
        }

        if ($success) {
            // Remove the request
            $this->manager->remove($request);
        } else {
            // Mark request for error
            $request->setState(ConversionRequest::STATE_ERROR);
            $this->manager->persist($request);
        }

        $this->manager->flush();
    }
}
