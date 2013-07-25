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
                        ->booleanNode('load_distribution')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('orphanage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('maxage')->defaultValue(604800)->end()
                        ->scalarNode('directory')->defaultNull()->end()
                    ->end()
                ->end()
                ->scalarNode('twig')->defaultTrue()->end()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('id')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->enumNode('frontend')
                                ->values(array('fineuploader', 'blueimp', 'uploadify', 'yui3', 'fancyupload', 'mooupload', 'plupload', 'custom'))
                                ->defaultValue('fineuploader')
                            ->end()
                            ->arrayNode('custom_frontend')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')->defaultNull()->end()
                                    ->scalarNode('class')->defaultNull()->end()
                                ->end()
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
                                    ->scalarNode('sync_buffer_size')->defaultValue('100K')->end()
                                ->end()
                            ->end()
                            ->arrayNode('allowed_extensions')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('disallowed_extensions')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('allowed_mimetypes')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('disallowed_mimetypes')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('error_handler')->defaultValue('oneup_uploader.error_handler.noop')->end()
                            ->scalarNode('max_size')
                                ->defaultValue(\PHP_INT_MAX)
                                ->info('Set max_size to -1 for gracefully downgrade this number to the systems max upload size.')
                            ->end()
                            ->booleanNode('use_orphanage')->defaultFalse()->end()
                            ->booleanNode('enable_progress')->defaultFalse()->end()
                            ->booleanNode('enable_cancelation')->defaultFalse()->end()
                            ->scalarNode('namer')->defaultValue('oneup_uploader.namer.uniqid')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
