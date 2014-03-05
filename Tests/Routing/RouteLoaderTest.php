<?php

namespace Oneup\UploaderBundle\Tests\Routing;

use Oneup\UploaderBundle\Routing\RouteLoader;

class RouteLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRouteLoader()
    {
        $cat = 'GrumpyCatController';
        $dog = 'HelloThisIsDogController';

        $routeLoader = new RouteLoader(array(
            'cat' => array($cat, array(
                'enable_progress' => false,
                'enable_cancelation' => false,
                'route_prefix' => ''
            )),
            'dog' => array($dog, array(
                'enable_progress' => true,
                'enable_cancelation' => true,
                'route_prefix' => ''
            )),
        ));

        $routes = $routeLoader->load(null);

        // for code coverage
        $this->assertTrue($routeLoader->supports('grumpy', 'uploader'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
        $this->assertCount(4, $routes);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals($route->getDefault('_format'), 'json');
            $this->assertEquals($route->getRequirement('_method'), 'POST');
        }
    }

    public function testPrefixedRoutes()
    {
        $prefix = '/admin';
        $cat = 'GrumpyCatController';

        $routeLoader = new RouteLoader(array(
            'cat' => array($cat, array(
                'enable_progress' => false,
                'enable_cancelation' => false,
                'route_prefix' => $prefix
            ))
        ));

        $routes = $routeLoader->load(null);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals($route->getDefault('_format'), 'json');
            $this->assertEquals($route->getRequirement('_method'), 'POST');

            $this->assertEquals(0, strpos($route->getPath(), $prefix));
        }
    }
}
