<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\File\File;
use Gaufrette\Stream\Local as LocalStream;
use Gaufrette\StreamMode;
use Gaufrette\Filesystem;

use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;
use Oneup\UploaderBundle\Uploader\Deletable\DeletableManagerInterface;

class GaufretteStorage implements StorageInterface
{
    protected $filesystem;
    protected $deletableManager;
    
    public function __construct(Filesystem $filesystem, DeletableManagerInterface $deletableManager)
    {
        $this->filesystem = $filesystem;
        $this->deletableManager = $deletableManager;
    }
    
    public function upload(File $file, $name = null)
    {
        $path = $file->getPathname();
        $name = is_null($name) ? $file->getRelativePathname() : $name;
        
        $src = new LocalStream($path);
        $dst = $this->filesystem->createStream($name);
        
        // this is a somehow ugly workaround introduced
        // because the stream-mode is not able to create
        // subdirectories.
        if(!$this->filesystem->has($name))
            $this->filesystem->write($name, '', true);
        
        $src->open(new StreamMode('rb+'));
        $dst->open(new StreamMode('ab+'));
            
        while(!$src->eof())
        {
            $data = $src->read(100000);
            $written = $dst->write($data);
        }
            
        $dst->close();
        $src->close();
        
        return $this->filesystem->get($name);
    }
    
    public function remove($type, $uuid)
    {
        try
        {
            // get associated file path
            $name = $this->deletableManager->getFile($type, $uuid);
            
            if($this->filesystem->has($name))
            {
                $this->filesystem->delete($name);
            }
            
            // delete this reference anyway
            $this->deletableManager->removeFile($type, $uuid);
        }
        catch(\Exception $e)
        {
            // whoopsi, something went terribly wrong
            // better leave this method now and never look back
            return false;
        }
        
        return true;
    }
}