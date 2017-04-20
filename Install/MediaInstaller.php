<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Install;

use Ekyna\Bundle\InstallBundle\Install\AbstractInstaller;
use Ekyna\Bundle\MediaBundle\Factory\FolderFactoryInterface;
use Ekyna\Bundle\MediaBundle\Manager\FolderManagerInterface;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Repository\FolderRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MediaInstaller
 * @package Ekyna\Bundle\MediaBundle\Install
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MediaInstaller extends AbstractInstaller
{
    private FolderRepositoryInterface $repository;
    private FolderManagerInterface    $manager;
    private FolderFactoryInterface    $factory;


    /**
     * Constructor.
     *
     * @param FolderRepositoryInterface $repository
     * @param FolderManagerInterface    $manager
     * @param FolderFactoryInterface    $factory
     */
    public function __construct(
        FolderRepositoryInterface $repository,
        FolderManagerInterface $manager,
        FolderFactoryInterface $factory
    ) {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function install(Command $command, InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>[Media] Creating root folder:</info>');
        $this->createRootFolders($output);
        $output->writeln('');
    }

    /**
     * Creates root folders.
     *
     * @param OutputInterface $output
     */
    private function createRootFolders(OutputInterface $output): void
    {
        $name = FolderInterface::ROOT;

        $output->write(sprintf(
            '- <comment>%s</comment> %s ',
            ucfirst($name),
            str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT)
        ));

        if ($this->repository->findRoot()) {
            $output->writeln('already exists.');

            return;
        }

        $folder = $this->factory->create();
        $folder->setName($name);

        $this->manager->save($folder);

        $output->writeln('created.');
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'ekyna_media';
    }
}
