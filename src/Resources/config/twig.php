<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Oneup\UploaderBundle\Twig\Extension\UploaderExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('oneup_uploader.twig.extension.uploader', UploaderExtension::class)
             ->public()
             ->args([service('oneup_uploader.templating.uploader_helper')])
             ->tag('twig.extension');
};
