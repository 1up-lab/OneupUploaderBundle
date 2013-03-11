<?php

namespace Oneup\UploaderBundle\Uploder\Chunk;

use Symfony\Component\Filesystem\Filesystem;
use Oneup\UploaderBundle\Uploader\ChunkManagerInterface;

class ChunkManager implements ChunkManagerInterafce
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
        $fileSystem = new FileSystem();
        $fileSystem->remove($this->configuration['directory']);
        
        $this->warmup();
    }
}