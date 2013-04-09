<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Oneup\UploaderBundle\Controller\AbstractController;

abstract class AbstractChunkedController extends AbstractController
{
    abstract protected function parseChunkedRequest(Request $request);
    
    protected function handleChunkedUpload(UploadedFile $file)
    {
        // get basic container stuff
        $request = $this->container->get('request');
        $chunkManager = $this->container->get('oneup_uploader.chunk_manager');
        
        // reset uploaded to always have a return value
        $uploaded = null;
        
        // get information about this chunked request
        list($last, $uuid, $index, $orig) = $this->parseChunkedRequest($request);
        
        $chunkManager->addChunk($uuid, $index, $file, $orig);
        
        // if all chunks collected and stored, proceed
        // with reassembling the parts
        if($last)
        {
            // we'll take the first chunk and append the others to it
            // this way we don't need another file in temporary space for assembling
            $chunks = $chunkManager->getChunks($uuid);
            
            // assemble parts
            $assembled = $chunkManager->assembleChunks($chunks);
            $path = $assembled->getPath();
            
            // create a temporary uploaded file to meet the interface restrictions
            $uploadedFile = new UploadedFile($assembled->getPathname(), $assembled->getBasename(), null, null, null, true);
            
            // validate this entity and upload on success
            $this->validate($uploadedFile);
            $uploaded = $this->handleUpload($uploadedFile);
            
            $chunkManager->cleanup($path);
        }
        
        return $uploaded;
    }
}