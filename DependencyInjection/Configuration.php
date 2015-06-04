<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_media');

        $this->addPoolsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `pools` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addPoolsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('pools')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('file')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    'show.html'  => 'EkynaMediaBundle:Admin/File:show.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\MediaBundle\Entity\File')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\MediaBundle\Form\Type\FileType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\MediaBundle\Table\Type\FileType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                        ->arrayNode('image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    'list.html'  => 'EkynaMediaBundle:Admin/Image:list.html',
                                    'show.html'  => 'EkynaMediaBundle:Admin/Image:show.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\MediaBundle\Entity\Image')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\MediaBundle\Controller\Admin\ImageController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\MediaBundle\Form\Type\ImageType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\MediaBundle\Table\Type\ImageType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                        ->arrayNode('gallery')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue(array(
                                    'show.html'  => 'EkynaMediaBundle:Admin/Gallery:show.html',
                                ))->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\MediaBundle\Entity\Gallery')->end()
                                ->scalarNode('controller')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\MediaBundle\Form\Type\GalleryType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\MediaBundle\Table\Type\GalleryType')->end()
                                ->scalarNode('parent')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
