<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\FilesystemFile;
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
        $path = null === $path ? $name : sprintf('%s/%s', $path, $name);

        if ($file instanceof FilesystemFile) {
            $stream = fopen($file->getPathname(), 'r+b');
            $this->filesystem->putStream($path, $stream, array(
                'mimetype' => $file->getMimeType()
            ));

            if (is_resource($stream)) {
                fclose($stream);
            }

            $filesystem = new LocalFilesystem();
            $filesystem->remove($file->getPathname());

            return new FlysystemFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
        }

        if ($file instanceof FlysystemFile && $file->getFilesystem() === $this->filesystem) {
            $file->getFilesystem()->rename($file->getPath(), $path);

            return new FlysystemFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
        }

        $manager = new MountManager([
            'chunks' => $file->getFilesystem(),
            'dest' => $this->filesystem,
        ]);

        $manager->move(sprintf('chunks://%s', $file->getPathname()), sprintf('dest://%s', $path));

        return new FlysystemFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
    }
}
