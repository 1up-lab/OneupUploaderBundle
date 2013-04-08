<?php

namespace Oneup\UploaderBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    protected $controllers;
    
    public function __construct(array $controllers)
    {
        $this->controllers = $controllers;
    }
    
    public function supports($resource, $type = null)
    {
        return $type === 'uploader';
    }
    
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();
        
        foreach($this->controllers as $type => $service)
        {
            $upload = new Route(
                sprintf('/_uploader/%s/upload', $type),
                array('_controller' => $service . ':upload', '_format' => 'json'),
                array('_method' => 'POST')
            );
            
            $routes->add(sprintf('_uploader_%s', $type), $upload);
        }
        
        return $routes;
    }
}