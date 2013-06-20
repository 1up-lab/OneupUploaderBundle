<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Oneup\UploaderBundle\Controller\AbstractChunkedController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class BlueimpController extends AbstractChunkedController
{
    public function upload()
    {
        $request = $this->container->get('request');
        $response = new EmptyResponse();
        $files = $request->files;
        
        $chunked = !is_null($request->headers->get('content-range'));
        
        foreach($files as $file)
        {
            $file = $file[0];
            
            try {
                $chunked ?
                    $this->handleChunkedUpload($file, $response, $request) :
                    $this->handleUpload($file, $response, $request)
                ;
            } catch(UploadException $e) {
                // return nothing
                return new JsonResponse(array());
            }
        }
        
        return new JsonResponse($response->assemble());
    }
    
    protected function parseChunkedRequest(Request $request)
    {
        $session = $this->container->get('session');
        $headerRange = $request->headers->get('content-range');
        $attachmentName = rawurldecode(preg_replace('/(^[^"]+")|("$)/', '', $request->headers->get('content-disposition')));
        
        // split the header string to the appropriate parts
        list($tmp, $startByte, $endByte, $totalBytes) = preg_split('/[^0-9]+/', $headerRange);
        
        // getting information about chunks
        // note: We don't have a chance to get the last $total
        // correct. This is due to the fact that the $size variable
        // is incorrect. As it will always be a higher number than
        // the one before, we just let that happen, if you have
        // any idea to fix this without fetching information about
        // previously saved files, let me know.
        $size  = ($endByte + 1 - $startByte);
        $last  = ($endByte + 1) == $totalBytes;
        $index = $last ? \PHP_INT_MAX : floor($startByte / $size);
        $total = ceil($totalBytes / $size);
        
        // it is possible, that two clients send a file with the
        // exact same filename, therefore we have to add the session
        // to the uuid otherwise we will get a mess
        $uuid  = md5(sprintf('%s.%s', $attachmentName, $session->getId()));
        $orig  = $attachmentName;
        
        return array($last, $uuid, $index, $orig);
    }
}