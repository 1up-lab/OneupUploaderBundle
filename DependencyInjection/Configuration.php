<?php

namespace Oneup\UploaderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
 
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('oneup_uploader');
        
        $rootNode
            ->children()
                ->arrayNode('chunks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('storage')->defaultNull()->end()
                        ->scalarNode('maxage')->defaultValue(604800)->end()
                    ->end()
                ->end()
                ->arrayNode('orphanage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                        ->scalarNode('storage')->defaultNull()->end()
                        ->scalarNode('maxage')->defaultValue(604800)->end()
                    ->end()
                ->end()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('id')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('storage')->isRequired()->end()
                            ->arrayNode('allowed_types')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('disallowed_types')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('max_size')->defaultValue(\PHP_INT_MAX)->end()
                            ->scalarNode('directory_prefix')->defaultNull()->end()
                            ->booleanNode('use_orphanage')->defaultFalse()->end()
                            ->booleanNode('deletable')->defaultFalse()->end()
                            ->scalarNode('namer')->defaultValue('oneup_uploader.namer.uniqid')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
 
        return $treeBuilder;
    }
}