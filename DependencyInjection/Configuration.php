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
                        ->scalarNode('maxage')->defaultValue(604800)->end()
                        ->scalarNode('directory')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('orphanage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('maxage')->defaultValue(604800)->end()
                        ->scalarNode('directory')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('id')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->enumNode('frontend')
                                ->values(array('fineuploader', 'blueimp', 'uploadify', 'yui3', 'fancyupload', 'mooupload'))
                                ->defaultValue('fineuploader')
                            ->end()
                            ->arrayNode('storage')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('service')->defaultNull()->end()
                                    ->enumNode('type')
                                        ->values(array('filesystem', 'gaufrette'))
                                        ->defaultValue('filesystem')
                                    ->end()
                                    ->scalarNode('filesystem')->defaultNull()->end()
                                    ->scalarNode('directory')->defaultNull()->end()
                                ->end()
                            ->end()
                            ->arrayNode('allowed_extensions')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('disallowed_extensions')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('max_size')->defaultValue(\PHP_INT_MAX)->end()
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