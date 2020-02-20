<?php

declare(strict_types=1);

namespace Oneup\UploaderBundle\Uploader\Gaufrette;

use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\Stream;
use Gaufrette\Stream\Local as LocalStream;
use Gaufrette\StreamMode;
use Oneup\UploaderBundle\Uploader\File\FileInterface;
use Oneup\UploaderBundle\Uploader\File\GaufretteFile;

class StreamManager
{
    /**
     * @var int
     */
    public $buffersize;

    /**
     * @var FilesystemInterface|Filesystem
     */
    protected $filesystem;

    protected function createSourceStream(FileInterface $file): Stream
    {
        if ($file instanceof GaufretteFile) {
            // The file is always streamable as the chunk storage only allows
            // adapters that implement StreamFactory
            return $file->createStream();
        }

        return new LocalStream($file->getPathname());
    }

    protected function ensureRemotePathExists(string $path): void
    {
        if (!$this->filesystem->has($path)) {
            $this->filesystem->write($path, '', true);
        }
    }

    protected function openStream(Stream $stream, string $mode): bool
    {
        // always use binary mode
        $mode .= 'b+';

        return $stream->open(new StreamMode($mode));
    }

    protected function stream(FileInterface $file, Stream $dst): void
    {
        $src = $this->createSourceStream($file);

        // always use reading only for the source
        $this->openStream($src, 'r');

        while (!$src->eof()) {
            $data = $src->read($this->buffersize);
            $dst->write($data);
        }

        $dst->close();
        $src->close();
    }
}
