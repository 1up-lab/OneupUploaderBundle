<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use League\Flysystem\Filesystem;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FlysystemFile;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

class FlysystemStorage implements StorageInterface
{

    /**
     * @var null|string
     */
    protected $streamWrapperPrefix;

    /**
     * @var float
     */
    protected $bufferSize;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem, $bufferSize, $streamWrapperPrefix = null)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        $path = is_null($path) ? $name : sprintf('%s/%s', $path, $name);

        if ($file instanceof FlysystemFile) {
            if ($file->getFilesystem() == $this->filesystem) {
                $file->getFilesystem()->rename($file->getPath(), $path);

                return new FlysystemFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
            }
        }

        $this->filesystem->put($name, file_get_contents($file));

        if ($file instanceof FlysystemFile) {
            $file->delete();
        } else {
            $filesystem = new LocalFilesystem();
            $filesystem->remove($file->getPathname());
        }

        return new FlysystemFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
    }

}
