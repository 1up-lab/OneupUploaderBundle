<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Oneup\UploaderBundle\Uploader\ErrorHandler\BlueimpErrorHandler;
use Oneup\UploaderBundle\Uploader\ErrorHandler\DropzoneErrorHandler;
use Oneup\UploaderBundle\Uploader\ErrorHandler\NoopErrorHandler;
use Oneup\UploaderBundle\Uploader\ErrorHandler\PluploadErrorHandler;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('oneup_uploader.error_handler.noop.class', NoopErrorHandler::class);
    $parameters->set('oneup_uploader.error_handler.blueimp.class', BlueimpErrorHandler::class);
    $parameters->set('oneup_uploader.error_handler.plupload.class', PluploadErrorHandler::class);
    $parameters->set('oneup_uploader.error_handler.dropzone.class', DropzoneErrorHandler::class);

    $services->set('oneup_uploader.error_handler.noop', '%oneup_uploader.error_handler.noop.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.fineuploader', '%oneup_uploader.error_handler.noop.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.blueimp', '%oneup_uploader.error_handler.blueimp.class%')
             ->private()
             ->args([service('translator')]);

    $services->set('oneup_uploader.error_handler.uploadify', '%oneup_uploader.error_handler.noop.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.yui3', '%oneup_uploader.error_handler.noop.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.fancyupload', '%oneup_uploader.error_handler.noop.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.mooupload', '%oneup_uploader.error_handler.noop.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.dropzone', '%oneup_uploader.error_handler.dropzone.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.plupload', '%oneup_uploader.error_handler.plupload.class%')
             ->private();

    $services->set('oneup_uploader.error_handler.custom', '%oneup_uploader.error_handler.noop.class%')
             ->private();
};
