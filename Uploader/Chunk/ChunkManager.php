<?php

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use Oneup\UploaderBundle\Uploader\Chunk\ChunkManagerInterface;
use Oneup\UploaderBundle\Uploader\Naming\NamerInterface;

class ChunkManager implements ChunkManagerInterface
{
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function warmup()
    {
        $filesystem = new FileSystem();
        $filesystem->mkdir($this->configuration['directory']);
    }
    
    public function clear()
    {
        $system = new Filesystem();
        $finder = new Finder();
        
        try
        {
            $finder->in($this->configuration['directory'])->date('<=' . -1 * (int) $this->configuration['maxage'] . 'seconds');
        }
        catch(\InvalidArgumentException $e)
        {
            // the finder will throw an exception of type InvalidArgumentException
            // if the directory he should search in does not exist
            // in that case we don't have anything to clean
            return;
        }
        
        foreach($finder as $file)
        {
            $system->remove($file);
        }
    }
    
    public function addChunk($uuid, $index, UploadedFile $chunk, $original)
    {
        $filesystem = new Filesystem();
        $path = sprintf('%s/%s', $this->configuration['directory'], $uuid);
        $name = sprintf('%s_%s', $index, $original);
        
        // create directory if it does not yet exist
        if(!$filesystem->exists($path))
            $filesystem->mkdir(sprintf('%s/%s', $this->configuration['directory'], $uuid));
        
        $chunk->move($path, $name);
    }
    
    public function assembleChunks(\Traversable $chunks)
    {
        // I don't really get it why getIterator()->current() always
        // gives me a null-value, due to that I've to implement this
        // in a rather unorthodox way.
        $i = 0;
        $base = null;
        
        foreach($chunks as $file)
        {
            if($i++ == 0)
            {
                $base = $file;
                
                // proceed with next files, as we have our
                // base data-container
                continue;
            }
            
            if(false === file_put_contents($base->getPathname(), file_get_contents($file->getPathname()), \FILE_APPEND | \LOCK_EX))
                throw new \RuntimeException('Reassembling chunks failed.');
        }
        
        return $base;
    }
    
    public function cleanup($path)
    {
        // cleanup
        $filesystem = new Filesystem();
        $filesystem->remove($path);
        
        return true;
    }
    
    public function getChunks($uuid)
    {
        $finder = new Finder();
        $finder
        ->in(sprintf('%s/%s', $this->configuration['directory'], $uuid))->files()->sort(function(\SplFileInfo $a, \SplFileInfo $b) {
            $t = explode('_', $a->getBasename());
            $s = explode('_', $b->getBasename());
            $t = (int) $t[0];
            $s = (int) $s[0];
                
            return $s < $t;
        });
        
        return $finder;
    }
}