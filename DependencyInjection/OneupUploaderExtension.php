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
        
        // handling chunk configuration
        if(!array_key_exists('directory', $config['chunks']))
        {
            $config['chunks']['directory'] = sprintf('%s/chunks', $container->getParameter('kernel.cache_dir'));
        }
        
        $container->setParameter('oneup_uploader.chunks', $config['chunks']);
    }
}