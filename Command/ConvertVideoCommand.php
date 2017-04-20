<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Command;

use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConvertVideoCommand
 * @package Ekyna\Bundle\MediaBundle\Command
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ConvertVideoCommand extends Command
{
    protected static $defaultName = 'ekyna:media:convert_video';

    private MediaRepositoryInterface $repository;
    private VideoManager $manager;


    public function __construct(MediaRepositoryInterface $repository, VideoManager $manager)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->manager    = $manager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The video id.')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The conversion formats.')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Whether to override existing conversions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = (int)$input->getArgument('id');

        if (!$video = $this->repository->find($id)) {
            $output->writeln('Media not found');

            return Command::FAILURE;
        }

        $formats = $input->getOption('format');
        if (empty($formats)) {
            $formats = MediaFormats::getFormatsByType(MediaTypes::VIDEO);
        }

        $override = (bool)$input->getOption('override');

        foreach ($formats as $format) {
            $output->write('Converting to <comment>' . strtoupper($format) . '</comment> ... ');

            $this->manager->convertVideo($video, $format, $override);

            $output->writeln('<info>done</info>');
        }

        return Command::SUCCESS;
    }
}
