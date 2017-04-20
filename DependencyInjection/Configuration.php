<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

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
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('ekyna_media');

        $root = $builder->getRootNode();

        $root
            ->children()
                ->arrayNode('image')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('quality')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('jpeg')->defaultValue(80)->end()
                                ->integerNode('png')->defaultValue(80)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('video')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('directory')->defaultValue('cache/video')->end()
                        ->scalarNode('watermark')->defaultNull()->end()
                        ->scalarNode('pending')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('ffmpeg')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('ffmpeg_binary')
                            ->defaultValue('/usr/local/bin/ffmpeg')
                            ->isRequired()
                        ->end()
                        ->scalarNode('ffprobe_binary')
                            ->defaultValue('/usr/local/bin/ffprobe')
                            ->isRequired()
                        ->end()
                        ->integerNode('binary_timeout')->defaultValue(60)->end()
                        ->integerNode('threads_count')->defaultValue(4)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
