<?php

namespace Oneup\UploaderBundle\Tests\Routing;

use Oneup\UploaderBundle\Routing\RouteLoader;
use PHPUnit\Framework\TestCase;

class RouteLoaderTest extends TestCase
{
    public function testRouteLoader()
    {
        $cat = 'GrumpyCatController';
        $dog = 'HelloThisIsDogController';

        $routeLoader = new RouteLoader([
            'cat' => [$cat, [
                'enable_progress' => false,
                'enable_cancelation' => false,
                'route_prefix' => '',
                'endpoints' => [
                    'upload' => null,
                    'progress' => null,
                    'cancel' => null,
                ],
            ]],
            'dog' => [$dog, [
                'enable_progress' => true,
                'enable_cancelation' => true,
                'route_prefix' => '',
                'endpoints' => [
                    'upload' => null,
                    'progress' => null,
                    'cancel' => null,
                ],
            ]],
        ]);

        $routes = $routeLoader->load(null);

        // for code coverage
        $this->assertTrue($routeLoader->supports('grumpy', 'uploader'));
        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $routes);
        $this->assertCount(4, $routes);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertSame('json', $route->getDefault('_format'));
            $this->assertContains('POST', $route->getMethods());
        }
    }

    public function testPrefixedRoutes()
    {
        $prefix = '/admin';
        $cat = 'GrumpyCatController';

        $routeLoader = new RouteLoader([
            'cat' => [$cat, [
                'enable_progress' => false,
                'enable_cancelation' => false,
                'route_prefix' => $prefix,
                'endpoints' => [
                    'upload' => null,
                    'progress' => null,
                    'cancel' => null,
                ],
            ]],
        ]);

        $routes = $routeLoader->load(null);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertSame('json', $route->getDefault('_format'));
            $this->assertContains('POST', $route->getMethods());

            $this->assertSame(0, strpos($route->getPath(), $prefix));
        }
    }

    public function testCustomEndpointRoutes()
    {
        $customEndpointUpload = '/grumpy/cats/upload';
        $cat = 'GrumpyCatController';

        $routeLoader = new RouteLoader([
            'cat' => [$cat, [
                'enable_progress' => false,
                'enable_cancelation' => false,
                'route_prefix' => '',
                'endpoints' => [
                    'upload' => $customEndpointUpload,
                    'progress' => null,
                    'cancel' => null,
                ],
            ]],
        ]);

        $routes = $routeLoader->load(null);

        foreach ($routes as $route) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $route);
            $this->assertSame('json', $route->getDefault('_format'));
            $this->assertContains('POST', $route->getMethods());

            $this->assertSame($customEndpointUpload, $route->getPath());
        }
    }
}
