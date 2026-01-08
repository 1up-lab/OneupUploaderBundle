<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Oneup\UploaderBundle\Command\ClearChunkCommand;
use Oneup\UploaderBundle\Command\ClearOrphansCommand;
use Oneup\UploaderBundle\Controller\BlueimpController;
use Oneup\UploaderBundle\Controller\DropzoneController;
use Oneup\UploaderBundle\Controller\FancyUploadController;
use Oneup\UploaderBundle\Controller\FineUploaderController;
use Oneup\UploaderBundle\Controller\MooUploadController;
use Oneup\UploaderBundle\Controller\PluploadController;
use Oneup\UploaderBundle\Controller\UploadifyController;
use Oneup\UploaderBundle\Controller\YUI3Controller;
use Oneup\UploaderBundle\Routing\RouteLoader;
use Oneup\UploaderBundle\Uploader\Chunk\ChunkManager;
use Oneup\UploaderBundle\Uploader\Naming\UniqidNamer;
use Oneup\UploaderBundle\Uploader\Naming\UrlSafeNamer;
use Oneup\UploaderBundle\Uploader\Orphanage\OrphanageManager;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemOrphanageStorage;
use Oneup\UploaderBundle\Uploader\Storage\FilesystemStorage;
use Oneup\UploaderBundle\Uploader\Storage\FlysystemStorage;
use Oneup\UploaderBundle\Uploader\Storage\GaufretteStorage;

return static function (ContainerConfigurator $container) {
    $services   = $container->services();
    $parameters = $container->parameters();
    $parameters->set('oneup_uploader.chunks.manager.class', ChunkManager::class);
    $parameters->set('oneup_uploader.chunks_storage.gaufrette.class', \Oneup\UploaderBundle\Uploader\Chunk\Storage\GaufretteStorage::class);
    $parameters->set('oneup_uploader.chunks_storage.flysystem.class', \Oneup\UploaderBundle\Uploader\Chunk\Storage\FlysystemStorage::class);
    $parameters->set('oneup_uploader.chunks_storage.filesystem.class', \Oneup\UploaderBundle\Uploader\Chunk\Storage\FilesystemStorage::class);
    $parameters->set('oneup_uploader.namer.urlsafename.class', UrlSafeNamer::class);
    $parameters->set('oneup_uploader.namer.uniqid.class', UniqidNamer::class);
    $parameters->set('oneup_uploader.routing.loader.class', RouteLoader::class);
    $parameters->set('oneup_uploader.storage.gaufrette.class', GaufretteStorage::class);
    $parameters->set('oneup_uploader.storage.flysystem.class', FlysystemStorage::class);
    $parameters->set('oneup_uploader.storage.filesystem.class', FilesystemStorage::class);
    $parameters->set('oneup_uploader.orphanage.class', FilesystemOrphanageStorage::class);
    $parameters->set('oneup_uploader.orphanage.manager.class', OrphanageManager::class);
    $parameters->set('oneup_uploader.controller.fineuploader.class', FineUploaderController::class);
    $parameters->set('oneup_uploader.controller.blueimp.class', BlueimpController::class);
    $parameters->set('oneup_uploader.controller.uploadify.class', UploadifyController::class);
    $parameters->set('oneup_uploader.controller.yui3.class', YUI3Controller::class);
    $parameters->set('oneup_uploader.controller.fancyupload.class', FancyUploadController::class);
    $parameters->set('oneup_uploader.controller.mooupload.class', MooUploadController::class);
    $parameters->set('oneup_uploader.controller.plupload.class', PluploadController::class);
    $parameters->set('oneup_uploader.controller.dropzone.class', DropzoneController::class);
    $parameters->set('oneup_uploader.command.clear_chunks.class', ClearChunkCommand::class);
    $parameters->set('oneup_uploader.command.clear_orphans.class', ClearOrphansCommand::class);

    $services->set('oneup_uploader.chunk_manager', '%oneup_uploader.chunks.manager.class%')
             ->public()
             ->args([
                        '%oneup_uploader.chunks%',
                        service('oneup_uploader.chunks_storage'),
                    ]);

    $services->set('oneup_uploader.orphanage_manager', '%oneup_uploader.orphanage.manager.class%')
             ->public()
             ->args([
                        service('service_container'),
                        '%oneup_uploader.orphanage%',
                    ]);

    $services->set('oneup_uploader.namer.uniqid', '%oneup_uploader.namer.uniqid.class%')
             ->public();

    $services->set('oneup_uploader.namer.urlsafe', '%oneup_uploader.namer.urlsafename.class%');

    $services->set('oneup_uploader.routing.loader', '%oneup_uploader.routing.loader.class%')
             ->public()
             ->args(['%oneup_uploader.controllers%'])
             ->tag('routing.loader');

    $services->set('oneup_uploader.command.clear_chunks', '%oneup_uploader.command.clear_chunks.class%')
             ->args([service('oneup_uploader.chunk_manager')])
             ->tag('console.command', ['command' => 'oneup:uploader:clear-chunks']);

    $services->set('oneup_uploader.command.clear_orphans', '%oneup_uploader.command.clear_orphans.class%')
             ->args([service('oneup_uploader.orphanage_manager')])
             ->tag('console.command', ['command' => 'oneup:uploader:clear-orphans']);
};
