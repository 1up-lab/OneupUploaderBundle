<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class FancyUploadController extends AbstractController
{
    public function upload()
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        foreach ($files as $file) {
            try {
                $this->handleUpload($file, $response, $request);
            } catch (UploadException $e) {
                $this->errorHandler->addException($response, $e);
            }
        }

        return $this->createSupportedJsonResponse($response->assemble());
    }
}
