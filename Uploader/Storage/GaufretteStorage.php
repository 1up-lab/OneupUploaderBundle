<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\MetadataSupporter;
use Oneup\UploaderBundle\Uploader\Gaufrette\StreamManager;

class GaufretteStorage extends StreamManager implements StorageInterface
{

    public function __construct(Filesystem $filesystem, $bufferSize)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
    }

    public function upload(FileInterface $file, $name, $path = null)
    {
        $path = is_null($path) ? $name : sprintf('%s/%s', $path, $name);

        if ($file instanceof GaufretteFile) {
            if ($file->getFilesystem() == $this->filesystem) {
                $file->getFilesystem()->rename($file->getKey(), $path);

                return $this->filesystem->get($path);
            }
        }

        if ($this->filesystem->getAdapter() instanceof MetadataSupporter) {
            $this->filesystem->getAdapter()->setMetadata($name, array('contentType' => $file->getMimeType()));
        }
        $this->ensureRemotePathExists($path.$name);
        $dst = $this->filesystem->createStream($path);

        $this->openStream($dst, 'w');

        $this->stream($file, $dst);

        return $this->filesystem->get($path);
    }

}
