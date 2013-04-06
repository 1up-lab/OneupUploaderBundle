<?php

namespace Oneup\UploaderBundle\Tests\Routing;

use Oneup\UploaderBundle\Routing\RouteLoader;

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    public function testRouteLoader()
    {
        $routeLoader = new RouteLoader();
        
        // for code coverage
        $this->assertTrue($routeLoader->supports('grumpy', 'uploader'));
        
        $cat = 'GrumpyCatController';
        $dog = 'HelloThisIsDogController';
        
        // add a new controller and check if the route will be added
        $routeLoader->addController('cat', $cat);
        $routeLoader->addController('dog', $dog);
        $routes = $routeLoader->load(null);
        
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
        $this->assertCount(2, $routes);
        
        foreach($routes as $route)
        {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals($route->getDefault('_format'), 'json');
            $this->assertEquals($route->getRequirement('_method'), 'POST');
        }
    }
}