<?php

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Controller\UploadControllerInterface;

class UploaderController implements UploadControllerInterface
{
    protected $namer;
    protected $storage;
    
    public function __construct($namer, $storage)
    {
        $this->namer = $namer;
        $this->storage = $storage;
    }
    
    public function upload()
    {
        /*
        $container = $this->container;
        $config = $this->mappings[$mapping];
        $request = $container->get('request');
        $files = $request->files;
        
        if(!$container->has($config['storage']))
            throw new \InvalidArgumentException(sprintf('The storage service "%s" must be defined.'));
        
        if(!$container->has($config['namer']))
            throw new \InvalidArgumentException(sprintf('The namer service "%s" must be defined.'));

        $storage = $container->get($config['storage']);
        $namer = $container->get($config['namer']);
        
        var_dump($request);
        
        die();
        foreach($files as $file)
        {
            var_dump($namer->name($file, $config));
        }
        */
        die();
    }
}