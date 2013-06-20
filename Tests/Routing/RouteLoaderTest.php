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
            'cat' => $cat,
            'dog' => $dog
        ));

        $routes = $routeLoader->load(null);

        // for code coverage
        $this->assertTrue($routeLoader->supports('grumpy', 'uploader'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
        $this->assertCount(2, $routes);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals($route->getDefault('_format'), 'json');
            $this->assertEquals($route->getRequirement('_method'), 'POST');
        }
    }
}
