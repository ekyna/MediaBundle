<?php

namespace Ekyna\Bundle\MediaBundle\Command;

use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaFormats;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Exception;
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

    /**
     * @var MediaRepository
     */
    private $repository;

    /**
     * @var VideoManager
     */
    private $manager;


    /**
     * Constructor.
     *
     * @param MediaRepository $repository
     * @param VideoManager    $manager
     */
    public function __construct(MediaRepository $repository, VideoManager $manager)
    {
        parent::__construct();

        $this->repository = $repository;
        $this->manager    = $manager;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'The video id.')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'The conversion formats.')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Whether to override existing conversions.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$video = $this->repository->find($input->getArgument('id'))) {
            throw new Exception("Media not found");
        }

        $formats = $input->getOption('format');
        if (empty($formats)) {
            $formats = MediaFormats::getFormatsByType(MediaTypes::VIDEO);
        }

        $override = $input->getOption('override');

        foreach ($formats as $format) {
            $output->write('Converting to <comment>' . strtoupper($format) . '</comment> ... ');

            $this->manager->convertVideo($video, $format, $override);

            $output->writeln('<info>done</info>');
        }
    }
}
