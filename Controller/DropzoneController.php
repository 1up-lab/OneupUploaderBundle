<?php

namespace Oneup\UploaderBundle\Controller;

use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class DropzoneController extends AbstractChunkedController
{
    public function upload()
    {
        $request = $this->getRequest();
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);
        $statusCode = 200;

        $chunked = !is_null($request->request->get('dzchunkindex'));

        foreach ($files as $file) {
            try {
                $chunked ?
                    $this->handleChunkedUpload($file, $response, $request) :
                    $this->handleUpload($file, $response, $request)
                ;
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

    protected function parseChunkedRequest(Request $request)
    {
        $totalChunkCount = $request->get('dztotalchunkcount');
        $index = $request->get('dzchunkindex');
        $last = ($index + 1) === (int) $totalChunkCount;
        $uuid = $request->get('dzuuid');

        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file')->getClientOriginalName();
        $orig = $file;

        return [$last, $uuid, $index, $orig];
    }
}
