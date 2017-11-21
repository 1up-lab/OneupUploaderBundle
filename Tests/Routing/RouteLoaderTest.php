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
                'route_prefix' => '',
                'endpoints' => array(
                    'upload' => null,
                    'progress' => null,
                    'cancel' => null,
                ),
            )),
            'dog' => array($dog, array(
                'enable_progress' => true,
                'enable_cancelation' => true,
                'route_prefix' => '',
                'endpoints' => array(
                    'upload' => null,
                    'progress' => null,
                    'cancel' => null,
                ),
            )),
        ));

        $routes = $routeLoader->load(null);

        // for code coverage
        $this->assertTrue($routeLoader->supports('grumpy', 'uploader'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
        $this->assertCount(4, $routes);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals('json', $route->getDefault('_format'));
            $this->assertContains('POST', $route->getMethods());
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
                'route_prefix' => $prefix,
                'endpoints' => array(
                    'upload' => null,
                    'progress' => null,
                    'cancel' => null,
                ),
            ))
        ));

        $routes = $routeLoader->load(null);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals('json', $route->getDefault('_format'));
            $this->assertContains('POST', $route->getMethods());

            $this->assertEquals(0, strpos($route->getPath(), $prefix));
        }
    }

    public function testCustomEndpointRoutes()
    {
        $customEndpointUpload = '/grumpy/cats/upload';
        $cat = 'GrumpyCatController';

        $routeLoader = new RouteLoader(array(
            'cat' => array($cat, array(
                'enable_progress' => false,
                'enable_cancelation' => false,
                'route_prefix' => '',
                'endpoints' => array(
                    'upload' => $customEndpointUpload,
                    'progress' => null,
                    'cancel' => null,
                ),
            ))
        ));

        $routes = $routeLoader->load(null);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals('json', $route->getDefault('_format'));
            $this->assertContains('POST', $route->getMethods());

            $this->assertEquals($customEndpointUpload, $route->getPath());
        }
    }
}
