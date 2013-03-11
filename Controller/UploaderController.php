<?php

namespace Oneup\UploaderBundle\Controller;

class UploaderController
{
    protected $mappings;
    protected $container;
    
    public function __construct($mappings, $container)
    {
        $this->mappings  = $mappings;
        $this->container = $container;
    }
    
    public function upload($mapping)
    {
        $container = $this->container;
        $config = $this->mappings[$mapping];
        
        if(!$container->has($config['storage']))
            throw new \InvalidArgumentException(sprintf('The storage service "%s" must be defined.'));
    }
}