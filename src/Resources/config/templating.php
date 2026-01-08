<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Oneup\UploaderBundle\Templating\Helper\UploaderHelper;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('oneup_uploader.templating.uploader_helper', UploaderHelper::class)
             ->public()
             ->args(
                 [
                     service('router'),
                     '%oneup_uploader.maxsize%',
                 ],
             );
};
