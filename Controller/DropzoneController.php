<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class DropzoneController extends AbstractController
{
    public function upload()
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);
        $statusCode = 200;
        foreach ($files as $file) {
            try {
                $this->handleUpload($file, $response, $request);
            } catch (UploadException $e) {
                $statusCode = 500; //Dropzone displays error if HTTP response is 40x or 50x
                $this->errorHandler->addException($response, $e);
                $translator = $this->container->get('translator');
                $message = $translator->trans($e->getMessage(), array(), 'OneupUploaderBundle');
                $response = $this->createSupportedJsonResponse(array('error'=>$message ));
                $response->setStatusCode(400);
                return $response;                
            }
        }

        return $this->createSupportedJsonResponse($response->assemble(), $statusCode);
    }
}
