<?php

namespace Oneup\UploaderBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
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
        return $type === 'uploader';
    }

    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();

        foreach ($this->controllers as $type => $controllerArray) {

            $service = $controllerArray[0];
            $options = $controllerArray[1];

            $upload = new Route(
                $options['endpoints']['upload'] ?: sprintf('%s/_uploader/%s/upload', $options['route_prefix'], $type),
                array('_controller' => $service . ':upload', '_format' => 'json'),
                array(),
                array(),
                '',
                array(),
                array('POST', 'PUT', 'PATCH')
            );

            if ($options['enable_progress'] === true) {
                $progress = new Route(
                    $options['endpoints']['progress'] ?: sprintf('%s/_uploader/%s/progress', $options['route_prefix'], $type),
                    array('_controller' => $service . ':progress', '_format' => 'json'),
                    array(),
                    array(),
                    '',
                    array(),
                    array('POST')
                );

                $routes->add(sprintf('_uploader_progress_%s', $type), $progress);
            }

            if ($options['enable_cancelation'] === true) {
                $progress = new Route(
                    $options['endpoints']['cancel'] ?: sprintf('%s/_uploader/%s/cancel', $options['route_prefix'], $type),
                    array('_controller' => $service . ':cancel', '_format' => 'json'),
                    array(),
                    array(),
                    '',
                    array(),
                    array('POST')
                );

                $routes->add(sprintf('_uploader_cancel_%s', $type), $progress);
            }

            $routes->add(sprintf('_uploader_upload_%s', $type), $upload);
        }

        return $routes;
    }
}
