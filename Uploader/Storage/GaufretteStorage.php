<?php

namespace Oneup\UploaderBundle\Uploader\Storage;

use Symfony\Component\HttpFoundation\File\File;
use Gaufrette\Stream\Local as LocalStream;
use Gaufrette\StreamMode;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\MetadataSupporter;

use Oneup\UploaderBundle\Uploader\Storage\StorageInterface;

class GaufretteStorage implements StorageInterface
{
    protected $filesystem;
    protected $bufferSize;

    public function __construct(Filesystem $filesystem, $bufferSize)
    {
        $this->filesystem = $filesystem;
        $this->bufferSize = $bufferSize;
    }

    public function upload(File $file, $name, $path = null)
    {
        $path = is_null($path) ? $name : sprintf('%s/%s', $path, $name);

        if ($this->filesystem->getAdapter() instanceof MetadataSupporter) {
            $this->filesystem->getAdapter()->setMetadata($name, array('contentType' => $file->getMimeType()));
        }

        $src = new LocalStream($file->getPathname());
        $dst = $this->filesystem->createStream($path);

        // this is a somehow ugly workaround introduced
        // because the stream-mode is not able to create
        // subdirectories.
        if(!$this->filesystem->has($path))
            $this->filesystem->write($path, '', true);

        $src->open(new StreamMode('rb+'));
        $dst->open(new StreamMode('wb+'));

        while (!$src->eof()) {
            $data = $src->read($this->bufferSize);
            $written = $dst->write($data);
        }

        $dst->close();
        $src->close();

        return $this->filesystem->get($path);
    }
}
