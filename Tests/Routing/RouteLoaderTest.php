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
            'cat' => array($cat, array('use_upload_progress' => false)),
            'dog' => array($dog, array('use_upload_progress' => true)),
        ));

        $routes = $routeLoader->load(null);

        // for code coverage
        $this->assertTrue($routeLoader->supports('grumpy', 'uploader'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
        $this->assertCount(3, $routes);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertEquals($route->getDefault('_format'), 'json');
            $this->assertEquals($route->getRequirement('_method'), 'POST');
        }
    }
}
