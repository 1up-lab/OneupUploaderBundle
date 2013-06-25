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
                sprintf('/_uploader/%s/upload', $type),
                array('_controller' => $service . ':upload', '_format' => 'json'),
                array('_method' => 'POST')
            );

            if ($options['enable_progress'] === true) {
                $progress = new Route(
                    sprintf('/_uploader/%s/progress', $type),
                    array('_controller' => $service . ':progress', '_format' => 'json'),
                    array('_method' => 'POST')
                );

                $routes->add(sprintf('_uploader_progress_%s', $type), $progress);
            }

            if ($options['enable_cancelation'] === true) {
                $progress = new Route(
                    sprintf('/_uploader/%s/cancel', $type),
                    array('_controller' => $service . ':cancel', '_format' => 'json'),
                    array('_method' => 'POST')
                );

                $routes->add(sprintf('_uploader_cancel_%s', $type), $progress);
            }

            $routes->add(sprintf('_uploader_upload_%s', $type), $upload);
        }

        return $routes;
    }
}
