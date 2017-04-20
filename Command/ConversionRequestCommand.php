<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\MediaBundle\Entity\ConversionRequest;
use Ekyna\Bundle\MediaBundle\Repository\ConversionRequestRepository;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

    private ConversionRequestRepository $repository;
    private VideoManager                $converter;
    private EntityManagerInterface      $manager;
    private bool                        $debug;


    public function __construct(
        ConversionRequestRepository $repository,
        VideoManager $converter,
        EntityManagerInterface $manager,
        bool $debug = false
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->converter = $converter;
        $this->manager = $manager;
        $this->debug = $debug;
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::OPTIONAL, 'The request ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument('id');

        if (0 < $id) {
            /** @var ConversionRequest $request */
            if (null === $request = $this->repository->find($id)) {
                $output->writeln('Request not found');

                return Command::FAILURE;
            }

            if ($request->getState() === ConversionRequest::STATE_RUNNING) {
                $output->writeln('Request is already running');

                return Command::SUCCESS;
            }

            $this->convert($request);

            return Command::SUCCESS;
        }

        // Abort if another request is running
        if ($this->repository->findRunning()) {
            return Command::SUCCESS;
        }

        // Abort if no other request to run
        if (!$request = $this->repository->findNext()) {
            return Command::SUCCESS;
        }

        $this->convert($request);

        return Command::SUCCESS;
    }

    private function convert(ConversionRequest $request): void
    {
        set_time_limit(5 * 60);

        $id = $request->getId();

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
        } catch (Throwable $throwable) {
            if ($this->debug) {
                /** @noinspection PhpUnhandledExceptionInspection */
                throw $throwable;
            }

            $success = false;
        }

        // If request has been deleted, abort
        if (!$request = $this->repository->find($id)) {
            // The media has been deleted -> clear converted video
            unlink($this->converter->getConvertedPath($media, $format));

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
