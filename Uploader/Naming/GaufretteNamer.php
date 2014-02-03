<?php

namespace Oneup\UploaderBundle\Uploader\Naming;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Gaufrette\Filesystem;

class GaufretteNamer implements NamerInterface
{
    private $filesystem;
    private $namer;

    public function __construct(Filesystem $filesystem, NamerInterface $namer)
    {
        $this->filesystem = $filesystem;
        $this->namer = $namer;
    }

    public function name(FileInterface $file)
    {
        do {
            $name = $this->namer->name($file);
        } while ($this->filesystem->has($name));

        return $name;
    }
}
