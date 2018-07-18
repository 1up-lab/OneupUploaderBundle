<?php

namespace Oneup\UploaderBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\Kernel;
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
        return 'uploader' === $type;
    }

    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();
        $separator = ':';

        // Double colon separators are used in Symfony >= 4.1 (see #340)
        if (version_compare(Kernel::VERSION, '4.1', '>=')) {
            $separator .= ':';
        }

        foreach ($this->controllers as $type => $controllerArray) {
            $service = $controllerArray[0];
            $options = $controllerArray[1];

            $upload = new Route(
                $options['endpoints']['upload'] ?: sprintf('%s/_uploader/%s/upload', $options['route_prefix'], $type),
                ['_controller' => $service.$separator.'upload', '_format' => 'json'],
                [],
                [],
                '',
                [],
                ['POST', 'PUT', 'PATCH']
            );

            if (true === $options['enable_progress']) {
                $progress = new Route(
                    $options['endpoints']['progress'] ?: sprintf('%s/_uploader/%s/progress', $options['route_prefix'], $type),
                    ['_controller' => $service.$separator.'progress', '_format' => 'json'],
                    [],
                    [],
                    '',
                    [],
                    ['POST']
                );

                $routes->add(sprintf('_uploader_progress_%s', $type), $progress);
            }

            if (true === $options['enable_cancelation']) {
                $progress = new Route(
                    $options['endpoints']['cancel'] ?: sprintf('%s/_uploader/%s/cancel', $options['route_prefix'], $type),
                    ['_controller' => $service.$separator.'cancel', '_format' => 'json'],
                    [],
                    [],
                    '',
                    [],
                    ['POST']
                );

                $routes->add(sprintf('_uploader_cancel_%s', $type), $progress);
            }

            $routes->add(sprintf('_uploader_upload_%s', $type), $upload);
        }

        return $routes;
    }
}
