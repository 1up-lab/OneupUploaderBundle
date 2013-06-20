<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;

use Oneup\UploaderBundle\Controller\AbstractController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class UploadifyController extends AbstractController
{
    public function upload()
    {
        $request = $this->container->get('request');
        $response = new EmptyResponse();
        $files = $request->files;
        
        foreach($files as $file)
        {
            try
            {
                $uploaded = $this->handleUpload($file, $response, $request);
            }
            catch(UploadException $e)
            {
                // return nothing
                return new JsonResponse(array());
            }
        }
        
        return new JsonResponse($response->assemble());
    }
}