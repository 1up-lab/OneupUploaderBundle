<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Gaufrette\Adapter\MetadataSupporter;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Oneup\UploaderBundle\Uploader\Gaufrette\StreamManager;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

class GaufretteStorage extends StreamManager implements StorageInterface
{
    protected $streamWrapperPrefix;

    /**
     * @param FilesystemInterface|Filesystem $filesystem
     * @param int                            $bufferSize
     * @param string|null                    $streamWrapperPrefix
     */
    public function __construct($filesystem, $bufferSize, $streamWrapperPrefix = null)
    {
        $base = interface_exists('Gaufrette\FilesystemInterface')
            ? 'Gaufrette\FilesystemInterface'
            : 'Gaufrette\Filesystem';

        if (!$filesystem instanceof $base) {
            throw new \InvalidArgumentException(sprintf('Expected an instance of "%s", got "%s".', $base, is_object($filesystem) ? get_class($filesystem) : gettype($filesystem)));
        }

        $this->filesystem = $filesystem;
        $this->buffersize = $bufferSize;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        $path = null === $path ? $name : sprintf('%s/%s', $path, $name);

        if ($this->filesystem->getAdapter() instanceof MetadataSupporter) {
            $this->filesystem->getAdapter()->setMetadata($name, ['contentType' => $file->getMimeType()]);
        }

        if ($file instanceof GaufretteFile) {
            if ($file->getFilesystem() === $this->filesystem) {
                $file->getFilesystem()->rename($file->getKey(), $path);

                return new GaufretteFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
            }
        }

        $this->ensureRemotePathExists($path);
        $dst = $this->filesystem->createStream($path);

        $this->openStream($dst, 'w');
        $this->stream($file, $dst);

        if ($file instanceof GaufretteFile) {
            $file->delete();
        } else {
            $filesystem = new LocalFilesystem();
            $filesystem->remove($file->getPathname());
        }

        return new GaufretteFile($this->filesystem->get($path), $this->filesystem, $this->streamWrapperPrefix);
    }
}
