<?php

namespace Oneup\UploaderBundle\Uploader\File;

use Gaufrette\File;
use Gaufrette\Filesystem;

class GaufretteFile extends File implements FileInterface
{
    protected $filesystem;

    public function __construct(File $file, Filesystem $filesystem) {
        parent::__construct($file->getKey(), $filesystem);
        $this->filesystem = $filesystem;
    }

    /**
     * Returns the size of the file
     *
     * !! WARNING !!
     * Calling this loads the entire file into memory,
     * in case of bigger files this could throw exceptions,
     * and will have heavy performance footprint.
     * !! ------- !!
     *
     * TODO mock/calculate the size if possible and use that instead?
     */
    public function getSize()
    {
        return parent::getSize();
    }

    public function getPath()
    {
        return pathinfo($this->getKey(), PATHINFO_DIRNAME);
    }

    public function getName()
    {
        return pathinfo($this->getKey(), PATHINFO_BASENAME);
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $this->getKey());
    }

    public function getExtension()
    {
        return pathinfo($this->getKey(), PATHINFO_EXTENSION);
    }

    public function getFilesystem()
    {
        return $this->filesystem;
    }

} 