<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteLoader extends Loader
{
    public function __construct(protected array $controllers)
    {
        parent::__construct();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return 'uploader' === $type;
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $routes = new RouteCollection();
        $separator = '::';

        foreach ($this->controllers as $controllerType => $controllerArray) {
            $service = $controllerArray[0];
            $options = $controllerArray[1];

            $upload = new Route(
                $options['endpoints']['upload'] ?: sprintf('%s/_uploader/%s/upload', $options['route_prefix'], $controllerType),
                ['_controller' => $service . $separator . 'upload', '_format' => 'json'],
                [],
                [],
                '',
                [],
                ['POST', 'PUT', 'PATCH']
            );

            if (true === $options['enable_progress']) {
                $progress = new Route(
                    $options['endpoints']['progress'] ?: sprintf('%s/_uploader/%s/progress', $options['route_prefix'], $controllerType),
                    ['_controller' => $service . $separator . 'progress', '_format' => 'json'],
                    [],
                    [],
                    '',
                    [],
                    ['POST']
                );

                $routes->add(sprintf('_uploader_progress_%s', $controllerType), $progress);
            }

            if (true === $options['enable_cancelation']) {
                $progress = new Route(
                    $options['endpoints']['cancel'] ?: sprintf('%s/_uploader/%s/cancel', $options['route_prefix'], $controllerType),
                    ['_controller' => $service . $separator . 'cancel', '_format' => 'json'],
                    [],
                    [],
                    '',
                    [],
                    ['POST']
                );

                $routes->add(sprintf('_uploader_cancel_%s', $controllerType), $progress);
            }

            $routes->add(sprintf('_uploader_upload_%s', $controllerType), $upload);
        }

        return $routes;
    }
}
