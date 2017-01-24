<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_media');

        $rootNode
            ->children()
                ->scalarNode('output_dir')->defaultValue('')->end()
                // TODO import filesystems
            ->end()
        ;

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
                        ->arrayNode('media')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('templates')->defaultValue([
                                    'list.html'  => 'EkynaMediaBundle:Admin/Media:list.html',
                                    'show.html'  => 'EkynaMediaBundle:Admin/Media:show.html',
                                ])->end()
                                ->scalarNode('entity')->defaultValue('Ekyna\Bundle\MediaBundle\Entity\Media')->end()
                                ->scalarNode('controller')->defaultValue('Ekyna\Bundle\MediaBundle\Controller\Admin\MediaController')->end()
                                ->scalarNode('repository')->defaultValue('Ekyna\Bundle\MediaBundle\Entity\MediaRepository')->end()
                                ->scalarNode('form')->defaultValue('Ekyna\Bundle\MediaBundle\Form\Type\MediaType')->end()
                                ->scalarNode('table')->defaultValue('Ekyna\Bundle\MediaBundle\Table\Type\MediaType')->end()
                                ->scalarNode('event')->end()
                                ->scalarNode('parent')->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('entity')->defaultValue('Ekyna\Bundle\MediaBundle\Entity\MediaTranslation')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('fields')
                                            ->prototype('scalar')->end()
                                                ->defaultValue(['title', 'description'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
