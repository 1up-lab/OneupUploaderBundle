<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Gaufrette\Filesystem;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;
use Gaufrette\Adapter\MetadataSupporter;
use Oneup\UploaderBundle\Uploader\Gaufrette\StreamManager;

class GaufretteStorage extends StreamManager implements StorageInterface
{
    protected $streamWrapperPrefix;

    public function __construct(Filesystem $filesystem, $bufferSize, $streamWrapperPrefix = null)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
        $this->streamWrapperPrefix = $streamWrapperPrefix;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        $path = is_null($path) ? $name : sprintf('%s/%s', $path, $name);

        if ($this->filesystem->getAdapter() instanceof MetadataSupporter) {
            $this->filesystem->getAdapter()->setMetadata($name, array('contentType' => $file->getMimeType()));
        }

        if ($file instanceof GaufretteFile) {
            if ($file->getFilesystem() == $this->filesystem) {
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
