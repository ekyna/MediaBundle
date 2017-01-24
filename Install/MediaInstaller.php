<?php

namespace Ekyna\Bundle\MediaBundle\Install;

use Ekyna\Bundle\InstallBundle\Install\AbstractInstaller;
use Ekyna\Bundle\MediaBundle\Entity\Folder;
use Ekyna\Bundle\InstallBundle\Install\OrderedInstallerInterface;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MediaInstaller
 * @package Ekyna\Bundle\MediaBundle\Install
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaInstaller extends AbstractInstaller implements OrderedInstallerInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function install(Command $command, InputInterface $input, OutputInterface $output)
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
    private function createRootFolders(OutputInterface $output)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $repository = $this->container->get('ekyna_media.folder.repository');

        $name = FolderInterface::ROOT;
        $output->write(sprintf(
            '- <comment>%s</comment> %s ',
            ucfirst($name),
            str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT)
        ));

        if (null !== $folder = $repository->findRoot()) {
            $output->writeln('already exists.');
        } else {
            $folder = new Folder();
            $folder->setName($name);

            $em->persist($folder);
            $em->flush();

            $output->writeln('created.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return -512;
    }
}
