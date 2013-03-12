<?php

namespace Oneup\UploaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
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
            if(!array_key_exists($mapping['directory_prefix']))
            {
                $mapping['directory_prefix'] = $key;
            }
            
            $this->registerServicesPerMap($container, $key, $mapping);
        }
    }
    
    public function registerServicesPerMap(ContainerBuilder $container, $type, $mapping)
    {
        // create controllers based on mapping
        $container
            ->register(sprintf('oneup_uploader.controller.%s', $type), $container->getParameter('oneup_uploader.controller.class'))
            
            ->addArgument(new Reference('request'))
            
            // add the correct namer as argument
            ->addArgument(new Reference($mapping['namer']))
            
            // add the correspoding storage service as argument    
            ->addArgument(new Reference($mapping['storage']))
                
            // after all, add the config as argument
            ->addArgument($mapping)
                
            ->addTag('oneup_uploader.routable', array('type' => $type))
            ->setScope('request')
        ;
    }
}