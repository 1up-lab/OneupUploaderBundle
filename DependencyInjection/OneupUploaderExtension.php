<?php

namespace Oneup\UploaderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class OneupUploaderExtension extends Extension
{
    protected $storageServices = array();

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('uploader.xml');
        $loader->load('templating.xml');
        $loader->load('validators.xml');
        $loader->load('errorhandler.xml');

        if ($config['twig']) {
            $loader->load('twig.xml');
        }

        $config['chunks']['directory'] = is_null($config['chunks']['directory']) ?
            sprintf('%s/uploader/chunks', $container->getParameter('kernel.cache_dir')) :
            $this->normalizePath($config['chunks']['directory'])
        ;

        $config['orphanage']['directory'] = is_null($config['orphanage']['directory']) ?
            sprintf('%s/uploader/orphanage', $container->getParameter('kernel.cache_dir')) :
            $this->normalizePath($config['orphanage']['directory'])
        ;

        $container->setParameter('oneup_uploader.chunks', $config['chunks']);
        $container->setParameter('oneup_uploader.orphanage', $config['orphanage']);

        $controllers = array();

        // handle mappings
        foreach ($config['mappings'] as $key => $mapping) {
            $mapping['max_size'] = $mapping['max_size'] < 0 ?
                $this->getMaxUploadSize($mapping['max_size']) :
                $mapping['max_size']
            ;

            // create the storage service according to the configuration
            $storageService = null;

            // if a service is given, return a reference to this service
            // this allows a user to overwrite the storage layer if needed
            if (!is_null($mapping['storage']['service'])) {
                $storageService = new Reference($mapping['storage']['service']);
            } else {
                // no service was given, so we create one
                $storageName = sprintf('oneup_uploader.storage.%s', $key);

                if ($mapping['storage']['type'] == 'filesystem') {
                    $mapping['storage']['directory'] = is_null($mapping['storage']['directory']) ?
                        sprintf('%s/../web/uploads/%s', $container->getParameter('kernel.root_dir'), $key) :
                        $this->normalizePath($mapping['storage']['directory'])
                    ;

                    $container
                        ->register($storageName, sprintf('%%oneup_uploader.storage.%s.class%%', $mapping['storage']['type']))
                        ->addArgument($mapping['storage']['directory'])
                    ;
                }

                if ($mapping['storage']['type'] == 'gaufrette') {
                    if(!class_exists('Gaufrette\\Filesystem'))
                        throw new InvalidArgumentException('You have to install Gaufrette in order to use it as a storage service.');

                    if(strlen($mapping['storage']['filesystem']) <= 0)
                        throw new ServiceNotFoundException('Empty service name');

                    $container
                        ->register($storageName, sprintf('%%oneup_uploader.storage.%s.class%%', $mapping['storage']['type']))
                        ->addArgument(new Reference($mapping['storage']['filesystem']))
                        ->addArgument($this->getValueInBytes($mapping['storage']['sync_buffer_size']))
                    ;
                }

                $storageService = new Reference($storageName);

                if ($mapping['use_orphanage']) {
                    $orphanageName = sprintf('oneup_uploader.orphanage.%s', $key);

                    // this mapping wants to use the orphanage, so create
                    // a masked filesystem for the controller
                    $container
                        ->register($orphanageName, '%oneup_uploader.orphanage.class%')
                        ->addArgument($storageService)
                        ->addArgument(new Reference('session'))
                        ->addArgument($config['orphanage'])
                        ->addArgument($key)
                    ;

                    // switch storage of mapping to orphanage
                    $storageService = new Reference($orphanageName);
                }
            }

            if ($mapping['frontend'] != 'custom') {
                $controllerName = sprintf('oneup_uploader.controller.%s', $key);
                $controllerType = sprintf('%%oneup_uploader.controller.%s.class%%', $mapping['frontend']);
            } else {
                $customFrontend = $mapping['custom_frontend'];

                $controllerName = sprintf('oneup_uploader.controller.%s', $customFrontend['name']);
                $controllerType = $customFrontend['class'];

                if(empty($controllerName) || empty($controllerType))
                    throw new ServiceNotFoundException('Empty controller class or name. If you really want to use a custom frontend implementation, be sure to provide a class and a name.');
            }

            $errorHandler = new Reference($mapping['error_handler']);

            // create controllers based on mapping
            $container
                ->register($controllerName, $controllerType)

                ->addArgument(new Reference('service_container'))
                ->addArgument($storageService)
                ->addArgument($errorHandler)
                ->addArgument($mapping)
                ->addArgument($key)

                ->addTag('oneup_uploader.routable', array('type' => $key))
                ->setScope('request')
            ;

            if ($mapping['enable_progress'] || $mapping['enable_cancelation']) {
                if (strnatcmp(phpversion(), '5.4.0') < 0) {
                    throw new InvalidArgumentException('You need to run PHP version 5.4.0 or above to use the progress/cancelation feature.');
                }
            }

            $controllers[$key] = array($controllerName, array(
                'enable_progress' => $mapping['enable_progress'],
                'enable_cancelation' => $mapping['enable_cancelation']
            ));
        }

        $container->setParameter('oneup_uploader.controllers', $controllers);
    }

    protected function getMaxUploadSize($input)
    {
        $input   = $this->getValueInBytes($input);
        $maxPost = $this->getValueInBytes(ini_get('upload_max_filesize'));
        $maxFile = $this->getValueInBytes(ini_get('post_max_size'));

        return min(min($input, $maxPost), $maxFile);
    }

    protected function getValueInBytes($input)
    {
        // see: http://www.php.net/manual/en/function.ini-get.php
        $input = trim($input);
        $last  = strtolower($input[strlen($input) - 1]);

        switch ($last) {
            case 'g': $input *= 1024;
            case 'm': $input *= 1024;
            case 'k': $input *= 1024;
        }

        return $input;
    }

    protected function normalizePath($input)
    {
        return rtrim($input, '/');
    }
}
