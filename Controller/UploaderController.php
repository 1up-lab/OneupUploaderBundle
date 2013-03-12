<?php

namespace Oneup\UploaderBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Gaufrette\Stream\Local as LocalStream;
use Gaufrette\StreamMode;

use Oneup\UploaderBundle\Controller\UploadControllerInterface;

class UploaderController implements UploadControllerInterface
{
    protected $namer;
    protected $storage;
    
    public function __construct($request, $namer, $storage, $config)
    {
        $this->request = $request;
        $this->namer = $namer;
        $this->storage = $storage;
        $this->config = $config;
    }
    
    public function upload()
    {
        $totalParts = $this->request->get('qqtotalparts', 1);
        
        return $totalParts > 1 ? $this->handleChunkedUpload() : $this->handleUpload();
    }
    
    protected function handleUpload()
    {
        // get filebag from request
        $files = $this->request->files;
        
        foreach($files as $file)
        {
            $name = $this->namer->name($file);
            $path = $file->getPathname();
            
            $src = new LocalStream($path);
            $dst = $this->storage->createStream($name);
            
            $src->open(new StreamMode('rb+'));
            $dst->open(new StreamMode('ab+'));
            
            while(!$src->eof())
            {
                $data = $src->read(100000);
                $written = $dst->write($data);
            }
            
            $dst->close();
            $src->close();
        }
        
        return new JsonResponse(array('success' => true));
    }
    
    protected function handleChunkedUpload()
    {
        
    }
}