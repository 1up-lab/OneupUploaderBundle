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
    protected $container;
    protected $config;

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->config = $this->processConfiguration($configuration, $configs);
        $this->container = $container;

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('uploader.xml');
        $loader->load('templating.xml');
        $loader->load('validators.xml');
        $loader->load('errorhandler.xml');

        if ($this->config['twig']) {
            $loader->load('twig.xml');
        }

        $this->createChunkStorageService();
        $this->processOrphanageConfig();

        $container->setParameter('oneup_uploader.chunks', $this->config['chunks']);
        $container->setParameter('oneup_uploader.orphanage', $this->config['orphanage']);

        $controllers = array();
        $maxsize = array();

        // handle mappings
        foreach ($this->config['mappings'] as $key => $mapping) {
            $controllers[$key] = $this->processMapping($key, $mapping);
            $maxsize[$key] = $this->getMaxUploadSize($mapping['max_size']);

            $container->setParameter(sprintf('oneup_uploader.config.%s', $key), $mapping);
        }

        $container->setParameter('oneup_uploader.config', $this->config);
        $container->setParameter('oneup_uploader.controllers', $controllers);
        $container->setParameter('oneup_uploader.maxsize', $maxsize);
    }

    protected function processOrphanageConfig()
    {
        if ($this->config['chunks']['storage']['type'] === 'filesystem') {
            $defaultDir = sprintf('%s/uploader/orphanage', $this->container->getParameter('kernel.cache_dir'));
        } else {
            $defaultDir = 'orphanage';
        }

        $this->config['orphanage']['directory'] = is_null($this->config['orphanage']['directory']) ? $defaultDir:
            $this->normalizePath($this->config['orphanage']['directory'])
        ;
    }

    protected function processMapping($key, &$mapping)
    {
        $mapping['max_size'] = $mapping['max_size'] < 0 ?
            $this->getMaxUploadSize($mapping['max_size']) :
            $mapping['max_size']
        ;
        $controllerName = $this->createController($key, $mapping);

        $this->verifyPhpVersion($mapping);

        return array($controllerName, array(
            'enable_progress' => $mapping['enable_progress'],
            'enable_cancelation' => $mapping['enable_cancelation'],
            'route_prefix' => $mapping['route_prefix']
        ));
    }

    protected function createController($key, $config)
    {
        // create the storage service according to the configuration
        $storageService = $this->createStorageService($config['storage'], $key, $config['use_orphanage']);

        if ($config['frontend'] != 'custom') {
            $controllerName = sprintf('oneup_uploader.controller.%s', $key);
            $controllerType = sprintf('%%oneup_uploader.controller.%s.class%%', $config['frontend']);
        } else {
            $customFrontend = $config['custom_frontend'];

            $controllerName = sprintf('oneup_uploader.controller.%s', $customFrontend['name']);
            $controllerType = $customFrontend['class'];

            if(empty($controllerName) || empty($controllerType))
                throw new ServiceNotFoundException('Empty controller class or name. If you really want to use a custom frontend implementation, be sure to provide a class and a name.');
        }

        $errorHandler = $this->createErrorHandler($config);

        // create controllers based on mapping
        $this->container
            ->register($controllerName, $controllerType)

            ->addArgument(new Reference('service_container'))
            ->addArgument($storageService)
            ->addArgument($errorHandler)
            ->addArgument($config)
            ->addArgument($key)

            ->addTag('oneup_uploader.routable', array('type' => $key))
            ->setScope('request')
        ;

        return $controllerName;
    }

    protected function createErrorHandler($config)
    {
        return is_null($config['error_handler']) ?
            new Reference('oneup_uploader.error_handler.'.$config['frontend']) :
            new Reference($config['error_handler']);
    }

    protected function verifyPhpVersion($config)
    {
        if ($config['enable_progress'] || $config['enable_cancelation']) {
            if (strnatcmp(phpversion(), '5.4.0') < 0) {
                throw new InvalidArgumentException('You need to run PHP version 5.4.0 or above to use the progress/cancelation feature.');
            }
        }
    }

    protected function createChunkStorageService()
    {
        $config = &$this->config['chunks']['storage'];

        $storageClass = sprintf('%%oneup_uploader.chunks_storage.%s.class%%', $config['type']);
        if ($config['type'] === 'filesystem') {
            $config['directory'] = is_null($config['directory']) ?
                 sprintf('%s/uploader/chunks', $this->container->getParameter('kernel.cache_dir')) :
                 $this->normalizePath($config['directory'])
            ;

            $this->container
                ->register('oneup_uploader.chunks_storage', sprintf('%%oneup_uploader.chunks_storage.%s.class%%', $config['type']))
                ->addArgument($config['directory'])
            ;
        } else {
            $this->registerGaufretteStorage(
                'oneup_uploader.chunks_storage',
                $storageClass, $config['filesystem'],
                $config['sync_buffer_size'],
                $config['stream_wrapper'],
                $config['prefix']
            );

            $this->container->setParameter('oneup_uploader.orphanage.class', 'Oneup\UploaderBundle\Uploader\Storage\GaufretteOrphanageStorage');

            // enforce load distribution when using gaufrette as chunk
            // torage to avoid moving files forth-and-back
            $this->config['chunks']['load_distribution'] = true;
        }
    }

    protected function createStorageService(&$config, $key, $orphanage = false)
    {
        $storageService = null;

        // if a service is given, return a reference to this service
        // this allows a user to overwrite the storage layer if needed
        if (!is_null($config['service'])) {
            $storageService = new Reference($config['service']);
        } else {
            // no service was given, so we create one
            $storageName = sprintf('oneup_uploader.storage.%s', $key);
            $storageClass = sprintf('%%oneup_uploader.storage.%s.class%%', $config['type']);

            if ($config['type'] == 'filesystem') {
                $config['directory'] = is_null($config['directory']) ?
                    sprintf('%s/../web/uploads/%s', $this->container->getParameter('kernel.root_dir'), $key) :
                    $this->normalizePath($config['directory'])
                ;

                $this->container
                    ->register($storageName, $storageClass)
                    ->addArgument($config['directory'])
                ;
            }

            if ($config['type'] == 'gaufrette') {
                $this->registerGaufretteStorage(
                    $storageName,
                    $storageClass,
                    $config['filesystem'],
                    $config['sync_buffer_size'],
                    $config['stream_wrapper']
                );
            }

            $storageService = new Reference($storageName);

            if ($orphanage) {
                $orphanageName = sprintf('oneup_uploader.orphanage.%s', $key);

                // this mapping wants to use the orphanage, so create
                // a masked filesystem for the controller
                $this->container
                    ->register($orphanageName, '%oneup_uploader.orphanage.class%')
                    ->addArgument($storageService)
                    ->addArgument(new Reference('session'))
                    ->addArgument(new Reference('oneup_uploader.chunks_storage'))
                    ->addArgument($this->config['orphanage'])
                    ->addArgument($key)
                ;

                // switch storage of mapping to orphanage
                $storageService = new Reference($orphanageName);
            }
        }

        return $storageService;
    }

    protected function registerGaufretteStorage($key, $class, $filesystem, $buffer, $streamWrapper = null, $prefix = '')
    {
        if(!class_exists('Gaufrette\\Filesystem'))
            throw new InvalidArgumentException('You have to install Gaufrette in order to use it as a chunk storage service.');

        if(strlen($filesystem) <= 0)
            throw new ServiceNotFoundException('Empty service name');

        $streamWrapper = $this->normalizeStreamWrapper($streamWrapper);

        $this->container
            ->register($key, $class)
            ->addArgument(new Reference($filesystem))
            ->addArgument($this->getValueInBytes($buffer))
            ->addArgument($streamWrapper)
            ->addArgument($prefix)
        ;
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

    protected function normalizeStreamWrapper($input)
    {
        if (is_null($input)) {
            return null;
        }

        return rtrim($input, '/') . '/';
    }
}
