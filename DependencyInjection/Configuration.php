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
        ;
 
        return $treeBuilder;
    }
}