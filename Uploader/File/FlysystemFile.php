<?php
namespace Oneup\UploaderBundle\Uploader\File;

use League\Flysystem\File;
use League\Flysystem\Filesystem;

class FlysystemFile extends File implements FileInterface
{

    protected $streamWrapperPrefix;
    protected $mimeType;

    public function __construct(File $file, Filesystem $filesystem, $streamWrapperPrefix = null)
    {
        parent::__construct($filesystem, $file->getPath());
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    /**
     * Returns the path of the file
     *
     * @return string
     */
    public function getPathname()
    {
        return $this->getPath();
    }

    /**
     * Returns the basename of the file
     *
     * @return string
     */
    public function getBasename()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    /**
     * Returns the guessed extension of the file
     *
     * @return mixed
     */
    public function getExtension()
    {
        return pathinfo($this->getPath(), PATHINFO_EXTENSION);
    }
}
