<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Oneup\UploaderBundle\Controller\AbstractController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class YUI3Controller extends AbstractController
{
    public function upload()
    {
        $request = $this->container->get('request');
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        foreach ($files as $file) {
            try {
                $uploaded = $this->handleUpload($file, $response, $request);
            } catch (UploadException $e) {
                $this->errorHandler->addException($response, $e);
            }
        }

        return new JsonResponse($response->assemble());
    }
}
