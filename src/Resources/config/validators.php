<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Oneup\UploaderBundle\EventListener\AllowedMimetypeAndExtensionValidationListener;
use Oneup\UploaderBundle\EventListener\DisallowedMimetypeValidationListener;
use Oneup\UploaderBundle\EventListener\MaxSizeValidationListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(MaxSizeValidationListener::class)
             ->tag('kernel.event_listener', ['event' => 'oneup_uploader.validation', 'method' => 'onValidate']);

    $services->set(AllowedMimetypeAndExtensionValidationListener::class)
             ->tag('kernel.event_listener', ['event' => 'oneup_uploader.validation', 'method' => 'onValidate']);

    $services->set(DisallowedMimetypeValidationListener::class)
             ->tag('kernel.event_listener', ['event' => 'oneup_uploader.validation', 'method' => 'onValidate']);
};
