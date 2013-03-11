<?php

namespace Oneup\UploaderBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    protected $action;
    protected $prefix;
    protected $mappings;
    
    public function __construct($action, $prefix, array $mappings)
    {
        $this->action = $action;
        $this->prefix = $prefix;
        $this->mappings = $mappings;
    }
    
    public function supports($resource, $type = null)
    {
        return $type === 'uploader';
    }
    
    public function load($resource, $type = null)
    {
        $requirements = array('_method' => 'POST', 'mapping' => '[A-z0-9_\-]*');
        $defaults = array('_controller' => $this->action);
        
        $routes = new RouteCollection();
        
        foreach($this->mappings as $key => $mapping)
        {
            $defaults += array('mapping' => $key);
            
            $routes->add(sprintf('_uploader_%s', $key), new Route(
                sprintf('%s/{mapping}', $this->prefix),
                $defaults,
                $requirements,
                array()
            ));
        }
        
        return $routes;
    }
}