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
                        ->scalarNode('directory')->end()
                        ->scalarNode('maxage')->defaultValue(604800)->end()
                    ->end()
                ->end()
                ->arrayNode('orphanage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('directory')->end()
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
                            ->scalarNode('directory_prefix')->end()
                            ->booleanNode('use_orphanage')->defaultFalse()->end()
                            ->scalarNode('namer')->defaultValue('oneup_uploader.namer.uniqid')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
 
        return $treeBuilder;
    }
}