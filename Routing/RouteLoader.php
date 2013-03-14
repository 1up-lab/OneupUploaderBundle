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
            $upload = new Route(
                sprintf('/_uploader/%s/upload', $type),
                array('_controller' => $service . ':upload', '_format' => 'json'),
                array('_method' => 'POST')
            );
            
            $delete = new Route(
                sprintf('/_uploader/%s/delete/{uuid}', $type),
                array('_controller' => $service . ':delete', '_format' => 'json'),
                array('_method' => 'DELETE', 'uuid' => '[A-z0-9-]*')
            );
            
            $base = new Route(
                sprintf('/_uploader/%s/delete', $type),
                array('_controller' => $service . ':delete', '_format' => 'json'),
                array('_method' => 'DELETE')
            );
            
            $routes->add(sprintf('_uploader_%s_upload', $type), $upload);
            $routes->add(sprintf('_uploader_%s_delete', $type), $delete);
            $routes->add(sprintf('_uploader_%s_delete_base', $type), $base);
        }
        
        return $routes;
    }
}