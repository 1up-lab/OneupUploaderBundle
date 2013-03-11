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
                ->arrayNode('routing')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('prefix')->defaultValue('/oneup/uploader')->end()
                        ->scalarNode('action')->defaultValue('OneupUploaderBundle:Uploader:upload')->end()
                    ->end()
                ->end()
                ->arrayNode('chunks')
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
                            ->scalarNode('namer')->defaultNull()->end()
                            ->scalarNode('storage')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
 
        return $treeBuilder;
    }
}