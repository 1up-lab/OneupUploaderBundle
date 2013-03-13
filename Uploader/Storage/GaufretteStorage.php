<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\File\File;
use Gaufrette\Stream\Local as LocalStream;
use Gaufrette\StreamMode;
use Gaufrette\Filesystem;
use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;

class GaufretteStorage implements StorageInterface
{
    protected $filesystem;
    
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    public function upload(File $file, $name)
    {
        $path = $file->getPathname();
            
        $src = new LocalStream($path);
        $dst = $this->filesystem->createStream($name);
        
        // this is a somehow ugly workaround introduced
        // because the stream-mode is not able to create
        // subdirectories.
        if(!$this->filesystem->has($name))
            $this->filesystem->createFile($name);
        
        $src->open(new StreamMode('rb+'));
        $dst->open(new StreamMode('ab+'));
            
        while(!$src->eof())
        {
            $data = $src->read(100000);
            $written = $dst->write($data);
        }
            
        $dst->close();
        $src->close();
        
        return true;
    }
    
    public function remove(File $file)
    {
        
    }
}