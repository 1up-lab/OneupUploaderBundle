<?php

namespace Oneup\UploaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class OneupUploaderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
 
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('uploader.xml');
        
        // handle routing configuration
        foreach($config['routing'] as $key => $value)
        {
            $container->setParameter(sprintf('oneup_uploader.routing.%s', $key), $value);
        }
        
        // handling chunk configuration
        if(!array_key_exists('directory', $config['chunks']))
        {
            $config['chunks']['directory'] = sprintf('%s/uploader/chunks', $container->getParameter('kernel.cache_dir'));
        }
        
        $container->setParameter('oneup_uploader.chunks', $config['chunks']);
        
        // handling orphanage configuration
        if(!array_key_exists('directory', $config['orphanage']))
        {
            $config['orphanage']['directory'] = sprintf('%s/uploader/orphanage', $container->getParameter('kernel.cache_dir'));
        }
        
        $container->setParameter('oneup_uploader.orphanage', $config['orphanage']);
        
        // handle mappings
        foreach($config['mappings'] as $key => $mapping)
        {
            $container->setParameter(sprintf('oneup_uploader.mapping.%s', $key), $value);
        }
        
        $container->setParameter('oneup_uploader.mappings', $config['mappings']);
    }
}