<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Controller\AbstractChunkedController;
use Oneup\UploaderBundle\Uploader\Response\FineUploaderResponse;

class FineUploaderController extends AbstractChunkedController
{
    public function upload()
    {
        $request = $this->container->get('request');
        $translator = $this->container->get('translator');
        
        $response = new FineUploaderResponse();
        $totalParts = $request->get('qqtotalparts', 1);
        $files = $request->files;
        $chunked = $totalParts > 1;
        
        foreach($files as $file)
        {
            try
            {
                $uploaded = $chunked ? $this->handleChunkedUpload($file) : $this->handleUpload($file);
                
                // dispatch POST_PERSIST AND POST_UPLOAD events
                $this->dispatchEvents($uploaded, $response, $request);
            }
            catch(UploadException $e)
            {
                $response->setSuccess(false);
                $response->setError($translator->trans($e->getMessage(), array(), 'OneupUploaderBundle'));
                
                // an error happended, return this error message.
                return new JsonResponse($response->assemble());
            }
        }
        
        return new JsonResponse($response->assemble());
    }
    
    protected function parseChunkedRequest(Request $request)
    {
        $index = $request->get('qqpartindex');
        $total = $request->get('qqtotalparts');
        $uuid  = $request->get('qquuid');
        $orig  = $request->get('qqfilename');
        $last  = ($total - 1) == $index;
        
        return array($last, $uuid, $index, $orig);
    }
}