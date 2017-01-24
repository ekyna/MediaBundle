<?php

namespace Ekyna\Bundle\MediaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture as Loader;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ekyna\Bundle\MediaBundle\Entity\FolderRepository;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class LoadMediaData
 * @package Ekyna\Bundle\MediaBundle\DataFixtures\ORM
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class LoadMediaData extends Loader implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var FolderRepository
     */
    private $folderRepository;

    /**
     * @var MediaRepository
     */
    private $mediaRepository;


    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        $this->faker = Factory::create($this->container->getParameter('hautelook_alice.locale'));

        $this->folderRepository = $this->container->get('ekyna_media.folder.repository');
        $this->mediaRepository = $this->container->get('ekyna_media.media.repository');
    }

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $om)
    {
        $this->loadFolders($om);
        $this->loadRootMedias($om);

        foreach (MediaTypes::getConstants() as $const) {
            $this->loadMedias($om, $const);
        }
    }

    /**
     * Load the folders.
     *
     * @param ObjectManager $om
     */
    private function loadFolders(ObjectManager $om)
    {
        for ($f = 1; $f <= 3; $f++) {
            $folder = $this->folderRepository->createNew();
            $folder->setName($this->faker->sentence(rand(2,3)));
            $om->persist($folder);

            $sfCount = rand(0, 2);
            for ($sf = 1; $sf <= $sfCount; $sf++) {
                $subFolder = $this->folderRepository->createNew();
                $subFolder->setName($this->faker->sentence(rand(2,3)));
                $this->folderRepository->persistAsLastChildOf($subFolder, $folder);
                $om->persist($subFolder);
            }
        }
        $om->flush();
    }

    /**
     * Load the root folder's medias.
     *
     * @param ObjectManager $om
     * @throws \Exception
     */
    private function loadRootMedias(ObjectManager $om)
    {
        $dir = realpath(__DIR__.sprintf('/../../Resources/fixtures/%s', MediaTypes::IMAGE));
        if (0 === strlen($dir)) {
            return;
        }
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" does not exists.', $dir));
        }

        $sources = [];
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $source = sprintf('%s/%s', $dir, $file);
            if (!file_exists($source)) {
                throw new \Exception(sprintf('File "%s" does not exists.', $source));
            }
            $sources[] = $source;
        }

        $root = $this->folderRepository->findRoot();
        for ($i = 0; $i < 20; $i++) {
            $source = $this->faker->randomElement($sources);
            $target = sprintf('%s/%d.jpg', sys_get_temp_dir(), $i);
            if (!copy($source, $target)) {
                throw new \Exception(sprintf('Failed to copy "%s" into "%s".', $source, $target));
            }

            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            $media = $this->mediaRepository->createNew();
            $media
                ->setTitle($this->faker->sentence(rand(2,3), false))
                //->setDescription('<p>'.$this->faker->sentence().'</p>')
                ->setFolder($root)
                ->setFile(new File($target))
                ->setType(MediaTypes::IMAGE)
            ;
            $om->persist($media);
        }
        $om->flush();
    }

    /**
     * Load the files.
     *
     * @param ObjectManager $om
     * @param string $type
     * @throws \Exception
     */
    private function loadMedias(ObjectManager $om, $type)
    {
        MediaTypes::isValid($type, true);

        $dir = realpath(__DIR__.sprintf('/../../Resources/fixtures/%s', $type));
        if (0 === strlen($dir)) {
            return;
        }
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" does not exists.', $dir));
        }

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $source = sprintf('%s/%s', $dir, $file);
            if (!file_exists($source)) {
                throw new \Exception(sprintf('File "%s" does not exists.', $source));
            }
            $target = sprintf('%s/%s', sys_get_temp_dir(), $file);
            if (!copy($source, $target)) {
                throw new \Exception(sprintf('Failed to copy "%s" into "%s".', $source, $target));
            }

            /** @var \Ekyna\Bundle\MediaBundle\Model\FolderInterface $folder */
            $folder = $this->folderRepository->findRandomOneBy([]);

            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            $media = $this->mediaRepository->createNew();
            $media
                ->setTitle($this->faker->sentence(rand(2,3), false))
                //->setDescription('<p>'.$this->faker->sentence().'</p>')
                ->setFolder($folder)
                ->setFile(new File($target))
                ->setType($type)
            ;
            $om->persist($media);
        }
        $om->flush();
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return -1;
    }
}
