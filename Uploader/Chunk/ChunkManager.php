<?php

namespace Oneup\UploaderBundle\Uploader\Chunk;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

use Oneup\UploaderBundle\Uploader\Chunk\ChunkManagerInterface;

class ChunkManager implements ChunkManagerInterface
{
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function warmup()
    {
        $fileSystem = new FileSystem();
        $fileSystem->mkdir($this->configuration['directory']);
    }
    
    public function clear()
    {
        $system = new Filesystem();
        $finder = new Finder();
        
        $finder->in($this->configuration['directory'])->date('<=' . -1 * (int) $this->configuration['maxage'] . 'seconds');
        
        foreach($finder as $file)
        {
            $system->remove($file);
        }
    }
}