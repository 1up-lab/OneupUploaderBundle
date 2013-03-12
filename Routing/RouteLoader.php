<?php

namespace Oneup\UploaderBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    protected $name;
    protected $controllers;
    
    public function __construct()
    {
        $this->controllers = array();
    }
    
    public function addController($type, $controller)
    {
        $this->controllers[$type] = $controller;
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
            $route = new Route(
                sprintf('/_uploader/%s', $type),
                array('_controller' => $service . ':upload'),
                array('_method' => 'POST')
            );
            
            $routes->add(sprintf('_uploader_%s', $type), $route);
        }
        
        return $routes;
    }
}